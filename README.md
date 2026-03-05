[![WordPress](https://img.shields.io/badge/WordPress-%2321759B.svg?logo=wordpress&logoColor=white)](https://wordpress.org/plugins/sa-ai-markdown/) [![Packagist Version](https://img.shields.io/packagist/v/serkanalgur/markdown-content-negotiator-for-llms)](https://packagist.org/packages/serkanalgur/markdown-content-negotiator-for-llms) ![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/sa-ai-markdown) [![WordPress Plugin Stars](https://img.shields.io/wordpress/plugin/stars/sa-ai-markdown)](https://wordpress.org/plugins/sa-ai-markdown/#reviews) [![PHP](https://img.shields.io/badge/php-%23777BB4.svg?&logo=php&logoColor=white)](#) [![GitHub Release](https://img.shields.io/github/v/release/serkanalgur/markdown-content-negotiator-for-llms)](https://github.com/serkanalgur/markdown-content-negotiator-for-llms/releases) ![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dw/sa-ai-markdown)


![Logo](assets/icon-256.png)
## Markdown Content Negotiator for LLMs

A WordPress plugin that detects when a request is made for content in Markdown format (via the `Accept: text/markdown` header) and serves a clean, pre-generated Markdown version of the page instead of HTML.

## 🚀 Features

- **Content Negotiation**: Detects `Accept: text/markdown` and bypasses the standard theme template.
- **YAML Frontmatter**: Automatically prepends metadata (Title, Date, Author, Categories, Permalink).
- **Automated Caching**: Uses WP-Cron to pre-generate Markdown for all published posts and pages to minimize response time.
- **LLM-Friendly Headers**:
  - `X-Markdown-Tokens`: Estimates token count using standard heuristics.
  - `X-Content-Signal`: Provides document metadata (type, depth, priority) and custom signals.
- **Customizable**: Admin settings page to select Post Types and configure global content signals.

## 📥 Installation

1. Clone or download this repository.
2. Upload the folder to your `wp-content/plugins/` directory.
3. Activate the plugin in the WordPress Admin.
4. Go to **Settings > AI Markdown** to configure your settings.

## 🛠 Usage

To request the Markdown version of a post, include the appropriate header in your HTTP request:

```bash
curl -H "Accept: text/markdown" https://yourdomain.com/your-post/
```

### Response Example

```markdown
---
title: "Hello World"
date: "2026-02-20 12:00:00"
author: "Serkan Algur"
permalink: "https://yourdomain.com/hello-world/"
categories: ["Uncategorized"]
---

# Hello World

Welcome to WordPress. This is your first post. Edit or delete it, then start writing!
```

## ⚙️ Configuration

Available under **Settings > AI Markdown**:

- **Enabled Post Types**: Select which post types (post, page, etc.) should be available in Markdown.
- **X-Content-Signal Extra**: Add custom global signals like `ai-train=yes, search=yes`.
- **Manual Regeneration**: Trigger a full cache refresh for all posts.


## Screenshot

![Settings Page Screenshot](screenshot.png)

## Changelog

### 1.0.8
* Misspelling fix

### 1.0.7
* Refactor avoid trademark of 'WP'

### 1.0.5 
* Name Change & Refactor plugin

### 1.0.4
* Security: Implemented Late Escaping for all echoed variables and generated data.
* Security: Added nonces and strict data sanitization for admin settings.
* Standards: Full compliance with WordPress PHP Coding Standards (WPCS).
* Refactor: Added complete Docblock documentation and standardized all hook callbacks.

### 1.0.3
* Ability to generate markdown when post changes.

### 1.0.2
* Featured Image support
* Description creation

### 1.0.1
* Code Block Conversion Support (pre and code)

### 1.0.0
* Inital Release

## 📝 License

GPL-2.0+
