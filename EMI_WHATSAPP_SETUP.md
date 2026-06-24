# EMI WhatsApp Reminder System

## Overview
This system automatically sends WhatsApp reminders to customers 5 days before their EMI payment is due.

## Setup Instructions

### 1. Configure WhatsApp API
Add the following environment variables to your `.env` file:

```env
WHATSAPP_API_KEY=your_meta_business_api_key_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id_here
WHATSAPP_BUSINESS_ACCOUNT_ID=your_business_account_id_here
```

**How to get these credentials:**
- Create a Meta Business Account (facebook.com/business)
- Create a WhatsApp Business Account
- Generate an API token with `messages:write` permission
- Get your Phone Number ID and Business Account ID from the WhatsApp Manager

### 2. Run Migration
```bash
php artisan migrate
```

This adds two new columns to the `vehicle_emis` table:
- `reminder_sent` (boolean) - tracks if reminder was sent
- `reminder_sent_at` (timestamp) - tracks when reminder was sent

### 3. Set Up Daily Scheduler
The reminder command is scheduled to run daily at 9:00 AM IST.

Ensure your Laravel scheduler is running:
```bash
php artisan schedule:work
```

Or add to your crontab:
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Manual Testing
To test the reminder system manually:
```bash
php artisan emi:send-reminders
```

## How It Works

1. **Payment Recorded**: When a payment is recorded, `next_due_date` auto-advances by 1 month
2. **Daily Check**: At 9:00 AM daily, the system checks for EMIs due in 5 days
3. **Reminder Sent**: If found, a WhatsApp message is sent to the customer's phone number
4. **Tracking**: The system marks the reminder as sent (`reminder_sent = true`)
5. **Next Cycle**: When the next payment is recorded, `reminder_sent` resets to false for the new due date

## Message Format

Example WhatsApp reminder message:

```
🚗 *EMI Payment Reminder*

Vehicle: MH01AB1234
Amount Due: ₹36,000.00
Due Date: 01 Jun 2026

Please ensure timely payment to avoid penalties.
Thank you!
```

## Customer Phone Number Setup

The system uses the phone number from the vehicle owner's user profile.
Make sure your `User` model has a `phone` column with phone numbers in the format:
- With country code: `919876543210` (for India: 91 + 10-digit number)
- This phone number will be used for WhatsApp messages

## Troubleshooting

### Reminders not sending?
1. Check if API credentials are correct in `.env`
2. Check logs: `tail -f storage/logs/laravel.log`
3. Verify customer has a phone number in their profile
4. Manually run: `php artisan emi:send-reminders`

### API Errors?
- WhatsApp API might require message templates for production
- Development accounts might have limitations on recipients
- Check Meta's WhatsApp API documentation

### Schedule not running?
- Ensure scheduler is running: `php artisan schedule:work`
- Check if cron job is set up on your server

## Future Enhancements

- [ ] Support for multiple reminder days (3 days, 1 day before)
- [ ] SMS fallback if WhatsApp fails
- [ ] Message templates for different scenarios (overdue, payment confirmation)
- [ ] Dashboard widget showing pending reminders
- [ ] Manual reminder send from admin panel

