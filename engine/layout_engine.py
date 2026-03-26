"""
layout_engine.py
────────────────
Resolves an anchor-based layout_schema.json into concrete EMU boxes.
Output is a dict of element_id → { x_emu, y_emu, w_emu, h_emu, font, ... }

All math is done in EMU (integer) to avoid float drift.
"""

import json
import math
import os


# ── PowerPoint EMU constant ────────────────────────────────────────────────────
EMU_PER_INCH = 914400
EMU_PER_PT   = 12700


def pt_to_emu(pt: float) -> int:
    return round(pt * EMU_PER_PT)


def load_schema(schema_path: str) -> dict:
    with open(schema_path, 'r', encoding='utf-8') as f:
        return json.load(f)


def resolve_layout(schema: dict, slide_w_emu: int, slide_h_emu: int, logo_override: dict = None) -> dict:
    """
    Given a schema and actual slide dimensions (EMU), return resolved element boxes.
    Returns: { element_id: { x, y, w, h, font, [logo_path] } }
    """
    resolved = {}

    # First pass: resolve non-text, non-image elements
    for el in schema.get('elements', []):
        eid = el['id']
        etype = el['type']
        anchor_to = el.get('anchor_to', 'slide')
        anchor_point = el.get('anchor_point', 'top_left')

        if anchor_to == 'slide':
            parent = {'x': 0, 'y': 0, 'w': slide_w_emu, 'h': slide_h_emu}
        else:
            parent = resolved.get(anchor_to)
            if parent is None:
                continue  # Dependency not resolved yet, skip (simple 1-pass)

        if etype == 'rect':
            w = round(el.get('w_ratio', 1.0) * slide_w_emu)
            h = round(el.get('h_ratio', 0.15) * slide_h_emu)

            # bottom_offset_ratio: leave a strip at the very bottom (e.g. for dark accent bar)
            bottom_offset = round(el.get('bottom_offset_ratio', 0.0) * slide_h_emu)

            if anchor_point == 'bottom_left':
                x = parent['x'] + round(el.get('x_ratio', 0.0) * slide_w_emu)
                y = parent['y'] + parent['h'] - h - bottom_offset - round(el.get('y_ratio', 0.0) * slide_h_emu)
            elif anchor_point == 'top_left':
                x = parent['x'] + round(el.get('x_ratio', 0.0) * slide_w_emu)
                y = parent['y'] + round(el.get('y_ratio', 0.0) * slide_h_emu)
            else:
                x = parent['x']
                y = parent['y']

            resolved[eid] = {
                'x': x, 'y': y, 'w': w, 'h': h,
                'fill_color': el.get('fill_color'),
                'z_index': el.get('z_index', 0),
                'type': etype
            }

        elif etype == 'image':
            # Logo is anchored to banner, centered vertically, with overlap above banner
            banner = resolved.get(el.get('anchor_to', 'banner'))
            if banner is None:
                continue

            size_h = round(el.get('size_ratio_h', 0.22) * slide_h_emu)
            size_w = size_h  # Square logo

            overlap = el.get('overlap_ratio', 0.35)
            dx = round(el.get('dx_ratio_w', 0.01) * slide_w_emu)

            x = banner['x'] + dx
            # Center logo so overlap% sticks above banner top
            y = banner['y'] - round(size_h * overlap)

            logo_path = None
            if logo_override and logo_override.get('path') and os.path.exists(logo_override['path']):
                logo_path = logo_override['path']

            resolved[eid] = {
                'x': x, 'y': y, 'w': size_w, 'h': size_h,
                'logo_path': logo_path,
                'border_color': el.get('border_color', 'FFD700'),
                'bg_color':     el.get('bg_color',     '004D40'),
                'z_index': el.get('z_index', 3),
                'type': etype
            }

        elif etype == 'text':
            anchor_box = resolved.get(el.get('anchor_to', 'banner'))
            if anchor_box is None:
                continue

            logo_box = resolved.get('logo')

            # "after_logo" strategy: text starts right of logo
            if el.get('content_box_mode') == 'after_logo' and logo_box:
                logo_right = logo_box['x'] + logo_box['w']
                gap_ratio  = el.get('logo_gap_ratio', 0.01)
                gap        = round(gap_ratio * slide_w_emu)
                text_x     = logo_right + gap
            else:
                text_x = anchor_box['x']

            pad_right = round(el.get('padding_right_ratio_w', 0.015) * slide_w_emu)
            text_w = (anchor_box['x'] + anchor_box['w']) - text_x - pad_right

            pad_top = round(el.get('padding_top_ratio_h', 0.0) * slide_h_emu)
            pad_btm = round(el.get('padding_bottom_ratio_h', 0.0) * slide_h_emu)

            text_y = anchor_box['y'] + pad_top
            text_h = anchor_box['h'] - pad_top - pad_btm

            font_def = el.get('font', {})

            # Internal text margins → EMU
            internal_margins = {
                'left':   pt_to_emu(el.get('internal_margin_left_pt', 7.2)),
                'right':  pt_to_emu(el.get('internal_margin_right_pt', 7.2)),
                'top':    pt_to_emu(el.get('internal_margin_top_pt', 0)),
                'bottom': pt_to_emu(el.get('internal_margin_bottom_pt', 0)),
            }

            resolved[eid] = {
                'x': text_x, 'y': text_y, 'w': text_w, 'h': text_h,
                'vertical_align': el.get('vertical_align', 'middle'),
                'text_align': el.get('text_align', 'center'),
                'internal_margins': internal_margins,
                'font': {
                    'family':        font_def.get('family', 'Georgia'),
                    'preferred_pt':  font_def.get('preferred_size_pt', 36),
                    'min_pt':        font_def.get('min_size_pt', 20),
                    'max_pt':        font_def.get('max_size_pt', 48),
                    'line_height':   font_def.get('line_height', 1.12),
                    'para_space_before': font_def.get('paragraph_spacing_before_pt', 0),
                    'para_space_after':  font_def.get('paragraph_spacing_after_pt', 0),
                    'color_hex': font_def.get('color_hex', 'FFD700'),
                    'shadow': font_def.get('shadow', True),
                    'stroke': font_def.get('stroke', True),
                },
                'z_index': el.get('z_index', 5),
                'type': etype
            }

    return resolved


def fit_font_size(lines: list, font_family: str, preferred_pt: float,
                    min_pt: float, max_pt: float,
                    box_w_emu: int, box_h_emu: int, line_height: float,
                    internal_margins_emu: dict) -> float:
    """
    Binary search the largest font size that makes all text fit inside the box.
    Uses Pillow for measurement.
    Returns font_pt (float).
    """
    try:
        from PIL import ImageFont, ImageDraw, Image
    except ImportError:
        return preferred_pt

    # Available area after internal margins
    avail_w_px = (box_w_emu - internal_margins_emu['left'] - internal_margins_emu['right']) / EMU_PER_INCH * 96
    avail_h_px = (box_h_emu - internal_margins_emu['top']  - internal_margins_emu['bottom']) / EMU_PER_INCH * 96

    all_text = '\n'.join(l.get('primary', '') for l in lines if l.get('primary'))

    def text_fits(pt: float) -> bool:
        size_px = round(pt * 96 / 72)
        try:
            font_path = f"{font_family.lower()}.ttf"
            pil_font = ImageFont.truetype(font_path, size_px)
        except Exception:
            try:
                pil_font = ImageFont.truetype("arial.ttf", size_px)
            except Exception:
                pil_font = ImageFont.load_default()

        dummy = Image.new('RGB', (1, 1))
        draw = ImageDraw.Draw(dummy)

        total_h = 0
        for line in lines:
            txt = line.get('primary', '')
            if not txt:
                continue
            bbox = draw.textbbox((0, 0), txt, font=pil_font)
            lw = bbox[2] - bbox[0]
            lh = bbox[3] - bbox[1]
            if lw > avail_w_px:
                return False
            total_h += lh * line_height

        return total_h <= avail_h_px

    # Binary search
    lo, hi = min_pt, min(preferred_pt, max_pt)

    # If preferred_pt already fits, use it
    if text_fits(preferred_pt):
        return preferred_pt

    # Otherwise search downward
    best = min_pt
    lo, hi = min_pt, preferred_pt
    for _ in range(12):
        mid = (lo + hi) / 2
        if text_fits(mid):
            best = mid
            lo = mid
        else:
            hi = mid

    return best
