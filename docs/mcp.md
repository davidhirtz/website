# Agents (MCP)

[← Documentation](README.md)

The site is an [MCP](https://modelcontextprotocol.io) server: an agent such as Claude reads and writes its content
through the same model layer as the admin, as one user and with that user's permissions. Translations, search,
redirects and the trail work as in the admin; every change shows up in the trail with the client it came through.
There is no delete, and a new record should be a draft until someone publishes it.

The tools are generic: `list_resources`, `describe_resource`, `find_records`, `get_record` (an image file with a
preview Claude can describe), `create_record`, `update_record`, `set_status`, `reorder` and `find_trail`. For files:
`attach_file` (from a public URL or as content Claude made), `detach_file`, `upload_link` (the admin page to upload
the user's own files on) and `generate_image`. For entries: `assign_categories`. The resources are entries, sections,
categories, blocks, their images, files, folders and redirects. The cms also offers the prompts `getting_started` (what
the connected user can do, in plain language: the first thing to give an editor), `translate_entry` and
`write_meta_descriptions`.

## Image generation

`generate_image` creates an image with OpenAI, Google Gemini, Stability AI or Adobe Firefly and attaches it as a
draft. A service is offered once there is a key to pay with: an editor's own, added under *Account → API keys*, or else
the site's in `config/params.php` (`openaiApiKey`, `geminiApiKey`, `stabilityApiKey`, or `fireflyClientId` with
`fireflyClientSecret`; at most 20 images per user and day). `modules.media.imageGeneratorKeys` narrows that to `site` or
`user`; `modules.media.imageGenerators` removes a service with `null`.

## Locally, with Claude Code

`.mcp.json` registers the server `cms`, which runs `php yii mcp/serve` in this folder under `YII_ENV=local`. It acts
as the owner (the first user), so it works once `php yii user/create` has run. Claude Code asks once before it starts
a server from a project file. Another user, or the read tools alone:

```json
"args": ["yii", "mcp/serve", "--user=editor@example.com", "--read-only"]
```

The same command reaches a server over SSH:

```bash
claude mcp add cms-live -- ssh deploy@example.com php /var/www/REPOSITORY/yii mcp/serve --user=me@example.com
```

## On the live site

`POST /mcp` is off (`components.mcp.enableHttp` in `config/prod.php`). Turn it on once the site is served over HTTPS:

```php
'mcp' => [
    'enableHttp' => true,
    'enableOAuth' => true,
],
```

Then add the URL `https://www.example.com/mcp` as a custom connector in the client, in claude.ai under *Settings →
Connectors → Add custom connector*, in ChatGPT under its connector settings. Signing in leads to the admin's consent
page, where the user approves the connection, read-only or read-write, and revokes it later under *Account → Connected
apps*. The page names the host the client's `client_id` belongs to and the host the code is sent to, so a client that
calls itself "Claude" from another host is easy to spot.

Any client that identifies itself with a `client_id` URL (a client metadata document) may ask. To allow only some,
list the prefixes their `client_id` must start with:

```php
'allowedClientIds' => ['https://claude.ai/'],
```

A client without OAuth uses a token instead, created under *Account → Access tokens* or with `php yii mcp/token --user=… [--read-only]
[--expires=90]`, and sent as a header:

```bash
claude mcp add --transport http cms https://www.example.com/mcp --header "Authorization: Bearer <token>"
```

Behind a proxy, `request.trustedHosts` must be set, or the endpoint names itself with `http://` and every token is
refused. Changing a password revokes every connection and token of that user.
