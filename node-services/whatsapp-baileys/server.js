import { makeWASocket, useMultiFileAuthState, DisconnectReason } from '@whiskeysockets/baileys';
import express from 'express';
import pino from 'pino';
import qrcode from 'qrcode';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';
import fs from 'fs';

const __dirname = dirname(fileURLToPath(import.meta.url));
const AUTH_DIR = join(__dirname, 'auth_info');
const PORT = 3001; // always internal — do not use process.env.PORT (that belongs to Nginx)

let sock = null;
let connected = false;
let qrCode = null;
let lastError = null;
let starting = false;
let connectedNumber = null;
let manualReset = false;

const app = express();
app.use(express.json({ limit: '20mb' }));

async function startSocket() {
  if (starting) return;
  starting = true;
  try {
    if (!fs.existsSync(AUTH_DIR)) fs.mkdirSync(AUTH_DIR, { recursive: true });

    const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);

    sock = makeWASocket({
      logger: pino({ level: 'silent' }),
      auth: state,
      browser: ['KovaiTrans', 'Chrome', '1.0'],
      keepAliveIntervalMs: 30000,
      connectTimeoutMs: 60000,
      defaultQueryTimeoutMs: 60000,
    });

    sock.ev.on('connection.update', (update) => {
      const { connection, lastDisconnect, qr } = update;

      if (qr) {
        qrCode = qr;
        lastError = null;
        starting = false;
        console.log('\n=== SCAN THIS QR WITH WHATSAPP ===');
        console.log('Open WhatsApp → Linked Devices → Link a Device\n');
      }

      if (connection === 'open') {
        connected = true;
        qrCode = null;
        lastError = null;
        starting = false;
        try {
          const userId = sock.user?.id || '';
          connectedNumber = userId.split(':')[0] || null;
        } catch { connectedNumber = null; }
        console.log('WhatsApp connected successfully!' + (connectedNumber ? ' Number: ' + connectedNumber : ''));
      }

      if (connection === 'close') {
        connected = false;
        starting = false;
        connectedNumber = null;

        if (manualReset) {
          manualReset = false;
          return;
        }

        console.log(`Disconnected, reconnecting with saved credentials...`);
        lastError = `Reconnecting...`;
        setTimeout(startSocket, 3000);
      }
    });

    sock.ev.on('creds.update', saveCreds);
  } catch (err) {
    starting = false;
    connected = false;
    lastError = `Init failed: ${err.message}`;
    console.error('startSocket error:', err);
  }
}

app.get('/health', (_req, res) => {
  res.json({ ok: true, connected });
});

app.get('/status', (_req, res) => {
  res.json({
    connected,
    qr: qrCode ? qrCode.substring(0, 50) + '...' : null,
    hasQr: !!qrCode,
    error: lastError,
    starting,
    number: connectedNumber,
  });
});

app.post('/reconnect', async (_req, res) => {
  manualReset = true;
  connected = false;
  qrCode = null;
  lastError = null;
  starting = false;
  connectedNumber = null;
  try {
    if (sock) { try { sock.end(null); } catch {} sock = null; }
    // Wipe auth_info so a fresh QR is always generated
    if (fs.existsSync(AUTH_DIR)) {
      fs.rmSync(AUTH_DIR, { recursive: true, force: true });
    }
    fs.mkdirSync(AUTH_DIR, { recursive: true });
  } catch {}
  // Small delay so the socket fully closes before restarting
  setTimeout(startSocket, 1000);
  res.json({ ok: true, message: 'Reconnecting...' });
});

app.get('/qr', async (_req, res) => {
  if (qrCode) {
    try {
      const dataUrl = await qrcode.toDataURL(qrCode, { width: 220, margin: 1 });
      res.json({ qr: qrCode, dataUrl, message: null });
    } catch {
      res.json({ qr: qrCode, dataUrl: null, message: 'QR available but image generation failed' });
    }
  } else if (connected) {
    res.json({ qr: null, dataUrl: null, message: 'Already connected' });
  } else {
    res.json({ qr: null, dataUrl: null, message: 'No QR available yet' });
  }
});

app.post('/send', async (req, res) => {
  const { to, message } = req.body;

  if (!to || !message) {
    return res.status(400).json({ error: 'Missing to or message' });
  }

  if (!connected || !sock) {
    return res.status(503).json({ error: 'WhatsApp not connected' });
  }

  try {
    const jid = to.includes('@s.whatsapp.net') ? to : `${to}@s.whatsapp.net`;
    const result = await sock.sendMessage(jid, { text: message });
    res.json({ ok: true, id: result?.key?.id });
  } catch (err) {
    console.error('Send error:', err);
    res.status(500).json({ error: err.message });
  }
});

// Send a document (PDF) — accepts base64-encoded file
app.post('/send-document', async (req, res) => {
  const { to, filename, mimetype, base64, caption } = req.body;

  if (!to || !base64 || !filename) {
    return res.status(400).json({ error: 'Missing to, filename, or base64' });
  }

  if (!connected || !sock) {
    return res.status(503).json({ error: 'WhatsApp not connected' });
  }

  try {
    const jid = to.includes('@s.whatsapp.net') ? to : `${to}@s.whatsapp.net`;
    const buffer = Buffer.from(base64, 'base64');

    const result = await sock.sendMessage(jid, {
      document: buffer,
      fileName: filename,
      mimetype: mimetype || 'application/pdf',
      caption: caption || '',
    });

    res.json({ ok: true, id: result?.key?.id });
  } catch (err) {
    console.error('Send document error:', err);
    res.status(500).json({ error: err.message });
  }
});

app.listen(PORT, () => {
  console.log(`WhatsApp Baileys service running on port ${PORT}`);
  startSocket();
});
