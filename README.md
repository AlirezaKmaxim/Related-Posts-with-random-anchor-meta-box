# Custom Related Posts – Random Anchor Text

A lightweight WordPress plugin that shows **related posts with featured images** and a **random custom anchor text** for each related link.

On every single post, you can define multiple custom anchor texts (per related post) in a meta box. When the plugin renders related posts, it picks **one random anchor text** for each related item — great for SEO experiments and improving internal linking diversity.

---

## Features

- ✅ Related posts based on **shared categories**
- ✅ Displays **featured image + text link** for each related post
- ✅ **Custom anchor texts per post** via a meta box
- ✅ Supports multiple anchors, separated by:
  - English comma `,`
  - Persian comma `،`
  - New lines
- ✅ Automatically falls back to **post title** if no custom anchor is set
- ✅ Shortcode-based: use anywhere in post content or templates
- ✅ Uses `WP_Query` with sensible performance flags

---

## How It Works

1. On each post edit screen, you get a meta box called **"انکر تکست‌های مرتبط"**.
2. In that box you can enter one or more anchor texts for that post (one per line or separated by commas).
3. On the front-end, when another post calls `[custom_related_posts]`, this plugin:
   - Finds **related posts in the same categories**.
   - For each related post, reads its saved anchor texts.
   - Picks **one random anchor** from that list.
   - Outputs the related post with:
     - A clickable **thumbnail** (if the post has a featured image)
     - A clickable **anchor text** beneath the image

If no custom anchors exist or the list is empty, it uses the **post title** as the link text.

---

## Installation

1. Download or clone this repository into your WordPress installation:

   ```bash
   wp-content/plugins/custom-related-posts/
