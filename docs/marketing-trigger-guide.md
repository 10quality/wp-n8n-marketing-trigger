# Marketing Trigger Guide

## Settings Page

Menu location:

- `Settings > Marketing Trigger Settings`

Tabs:

- `Webhooks`
- `Business`
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

`Business` fields:

- `Business name`
- `Phone number`
- `Email`
- `What we do`

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
- `Campaign goal` (text)
- `Target audience` (text)
- `Call to action` (multi select2 with WordPress pages)
- `Alternative call to action` (text)

Platform values are sanitized and stored as slug strings.

### Trigger Settings

- `Send test` button
- `Send` button
- Both buttons send requests via WordPress AJAX (`admin-ajax.php`), not form posts.
- Trigger responses are shown inline in the metabox feedback area.

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
        "additional_images": false,
        "goal": "Campaign goal",
        "target_audience": "Campaign target audience",
        "call_to_action": [
            {
                "id": 456,
                "title": "Call to action page title",
                "url": "https://example.com/call-to-action-page"
            }
        ],
        "alternative_call_to_action": "Alternative call to action"
    },
    "business": {
        "name": "Business name",
        "phone": "123-456-7890",
        "email": "example@example.com",
        "description": "Brief description of what the business does."
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
