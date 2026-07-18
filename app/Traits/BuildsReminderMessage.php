<?php

namespace App\Traits;

use App\Models\Vehicle;
use Carbon\Carbon;

/**
 * BuildsReminderMessage
 *
 * Shared trait used by SendDocumentReminderJob and DailyCheckInController.
 *
 * Builds a well-formatted WhatsApp message for vehicle document expiry reminders.
 *
 * Includes:
 *   - App Name
 *   - Vehicle Number
 *   - Document Type
 *   - Expiry Date (dd MMM YYYY)
 *   - Days Remaining (or overdue count)
 *
 * If a VehicleReminderConfig has a custom message template, placeholders are replaced:
 *   {{app_name}}        → config('app.name')
 *   {{vehicle_number}}  → vehicle registration number
 *   {{document_type}}   → e.g. Insurance, Permit, PUC
 *   {{expiry_date}}     → e.g. 30 Jul 2026
 *   {{days_remaining}}  → number (negative = overdue)
 */
trait BuildsReminderMessage
{
    public function buildMessage(Vehicle $vehicle, string $docLabel, Carbon $expiryDate, int $daysRemaining, $config): string
    {
        $appName = config('app.name', 'Trans ERP');

        // Use config template if available (replace all supported placeholders)
        if ($config && $config->message) {
            return str_replace(
                [
                    '{{app_name}}',
                    '{{vehicle_number}}',
                    '{{document_type}}',
                    '{{expiry_date}}',
                    '{{days_remaining}}',
                ],
                [
                    $appName,
                    $vehicle->vehicle_number,
                    $docLabel,
                    $expiryDate->format('d M Y'),
                    $daysRemaining,
                ],
                $config->message
            );
        }

        // ── Fallback: built-in formatted message ──────────────────────────────
        $vehicleNo  = $vehicle->vehicle_number;
        $expDateStr = $expiryDate->format('d M Y');
        $separator  = str_repeat('─', 30);

        if ($daysRemaining < 0) {
            $overdueDays = abs($daysRemaining);
            return implode("\n", [
                "🚨 *{$docLabel} Expired — Action Required*",
                "",
                $separator,
                "🏢 *{$appName}*",
                $separator,
                "",
                "🚛 *Vehicle No:* {$vehicleNo}",
                "📄 *Document:*  {$docLabel}",
                "📅 *Expired On:* {$expDateStr}",
                "⏰ *Overdue By:* {$overdueDays} day" . ($overdueDays > 1 ? 's' : ''),
                "",
                "❗ This document has already expired.",
                "Please renew it *immediately* to avoid",
                "penalties and legal issues.",
                "",
                $separator,
                "_Sent by {$appName} Vehicle Management_",
            ]);
        }

        if ($daysRemaining === 0) {
            return implode("\n", [
                "⚠️ *{$docLabel} Expires TODAY*",
                "",
                $separator,
                "🏢 *{$appName}*",
                $separator,
                "",
                "🚛 *Vehicle No:* {$vehicleNo}",
                "📄 *Document:*  {$docLabel}",
                "📅 *Expiry Date:* {$expDateStr}",
                "⏰ *Remaining:* Expires today!",
                "",
                "❗ Renew *immediately* to avoid service",
                "disruption and legal penalties.",
                "",
                $separator,
                "_Sent by {$appName} Vehicle Management_",
            ]);
        }

        if ($daysRemaining <= 7) {
            $urgency = "🔴 *Urgent — Only {$daysRemaining} day" . ($daysRemaining > 1 ? 's' : '') . " left!*";
        } elseif ($daysRemaining <= 15) {
            $urgency = "🟠 *{$daysRemaining} days remaining — Please renew soon.*";
        } else {
            $urgency = "🟡 *{$daysRemaining} days remaining — Renewal coming up.*";
        }

        return implode("\n", [
            "📋 *{$docLabel} Expiry Reminder*",
            "",
            $separator,
            "🏢 *{$appName}*",
            $separator,
            "",
            "🚛 *Vehicle No:* {$vehicleNo}",
            "📄 *Document:*  {$docLabel}",
            "📅 *Expiry Date:* {$expDateStr}",
            "⏰ *Days Left:* {$daysRemaining} day" . ($daysRemaining > 1 ? 's' : ''),
            "",
            $urgency,
            "",
            "Please ensure renewal is completed",
            "before the expiry date.",
            "",
            $separator,
            "_Sent by {$appName} Vehicle Management_",
        ]);
    }

    public function findMatchingConfig($configs, int $daysRemaining)
    {
        $durationDays = [
            'Last 30 Days' => 30,
            'Last 15 Days' => 15,
            'Last 10 Days' => 10,
            'Last 5 Days'  => 5,
            'Last 1 Day'   => 1,
        ];

        $best    = null;
        $bestMax = PHP_INT_MAX;

        foreach ($configs as $config) {
            $maxDays = $durationDays[$config->duration] ?? null;
            if ($maxDays !== null && $daysRemaining <= $maxDays && $maxDays < $bestMax) {
                $best    = $config;
                $bestMax = $maxDays;
            }
        }

        return $best;
    }
}
