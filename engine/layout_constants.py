"""
layout_constants.py
-------------------
Single source of truth for all slide coordinate math.
Both ppt_generator.py (Python) and ppt.blade.php (JS) must use these same values.

PowerPoint 16:9 standard slide dimensions:
  Width  = 13.33 inches  (10-inch wide at 75 DPI, but pptx uses EMU based on 13.33")
  Height = 7.5  inches
"""

# ──────────────────────────────────────────────────────────────────────────────
#  Slide canvas
# ──────────────────────────────────────────────────────────────────────────────
SLIDE_W = 13.33   # inches  (16:9 standard width)
SLIDE_H = 7.50    # inches  (16:9 standard height)

# ──────────────────────────────────────────────────────────────────────────────
#  Teal Banner  — anchored from BOTTOM
#  Design ref: Banner height ≈ 15% of screen height
# ──────────────────────────────────────────────────────────────────────────────
BANNER_H_RATIO  = 0.15                       # 15 % of slide height
BANNER_H        = SLIDE_H * BANNER_H_RATIO   # 1.125 inches

# ──────────────────────────────────────────────────────────────────────────────
#  Logo circle — sits flush with banner bottom, extends upward into green area
#  Design ref: Logo W:80px, H:80px at 1920px → 80/1920 = 4.16% of screen width
# ──────────────────────────────────────────────────────────────────────────────
LOGO_SIZE       = SLIDE_W * 0.12             # diameter ≈ 12% of slide width  (≈1.6")
LOGO_X          = 0.12                       # inches from left edge
LOGO_Y          = SLIDE_H - LOGO_SIZE        # bottom-flush: top of logo circle (inches from top)

# ──────────────────────────────────────────────────────────────────────────────
#  Main Text Box — sits INSIDE the banner, right of the logo
#  Bottom-anchored: y = SLIDE_H - BANNER_H
# ──────────────────────────────────────────────────────────────────────────────
TEXT_X          = LOGO_X + LOGO_SIZE + 0.1   # just right of logo  ≈ 1.82"
TEXT_Y          = SLIDE_H - BANNER_H         # = 6.375"  — exact bottom-anchor
TEXT_W          = SLIDE_W - TEXT_X - 0.1     # stretch to near right edge
TEXT_H          = BANNER_H                   # full banner height

# ──────────────────────────────────────────────────────────────────────────────
#  Font defaults
# ──────────────────────────────────────────────────────────────────────────────
FONT_NAME       = 'Georgia'
FONT_SIZE_PT    = 36          # reduced from 44 so TEXT_TO_FIT_SHAPE can breathe
FONT_COLOR_HEX  = 'FFD700'   # Gold
