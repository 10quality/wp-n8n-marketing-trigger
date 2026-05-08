# Marketing Trigger Guide

## Settings Page

Menu location:

- `Settings > Marketing Trigger Settings`

Tabs:

- `Webhooks`
- `Payload`

`Webhooks` fields:

- `Method` (disabled, default `POST`)
- `Test URL`
- `Production URL`
- Submit button label `Save Settings`
- Save success message `Settings saved successfully`

`Payload` tab:

- Section `Example`
- JSON `<pre>` payload structure matching trigger payload.

## Campaign Post Type

Post type:

- `marketing_campaign`

Admin menu:

- `Campaigns`

Editor behavior:

- Body placeholder: `Write your campaign instructions here (for AI)...`
- Save message: `Campaign saved successfully`

## Metaboxes

### Campaign Settings

- `Platforms` (multi select2)
- `With cover image` (switch)
- `Cover image instructions` (textarea, visible only when `With cover image` is enabled)
- `Additional images` (switch, visible only when `With cover image` is enabled)

Platform values are sanitized and stored as slug strings.

### Trigger Settings

- `Send test` button
- `Send` button

Button disable rules:

- `Send test` disabled when `Test URL` is empty.
- `Send` disabled when `Production URL` is empty.

## Webhook Payload

Payload format:

```json
{
    "send_at": "2024-01-01T12:00:00Z",
    "campaign_id": 123,
    "title": "Campaign title",
    "instructions": "Campaign body",
    "settings": {
        "platforms": ["wordpress-blog", "linkedin"],
        "with_cover_image": true,
        "cover_image_instructions": "Generate a cover image with a blue background and the campaign title in white text.",
        "additional_images": false
    }
}
```

Trigger success notices:

- `Test campaign sent successfully`
- `Campaign sent successfully`

## Testing

Run inside Docker WordPress container:

```bash
docker ps
docker exec -it 10quality-wordpress-1 bash
cd wp-content/plugins/n8n-marketing-trigger
./vendor/bin/phpunit
```