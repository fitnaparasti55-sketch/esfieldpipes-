<?php
// Script to generate high quality DWC pipe SVG assets
$sizes = [
    50 => ['od' => 63, 'color' => '#f59e0b', 'label' => '50mm ID / 63mm OD DWC OFC Duct'],
    75 => ['od' => 90, 'color' => '#eab308', 'label' => '75mm ID / 90mm OD DWC Power Duct'],
    100 => ['od' => 120, 'color' => '#0284c7', 'label' => '100mm ID / 120mm OD Sewerage Pipe'],
    150 => ['od' => 175, 'color' => '#0284c7', 'label' => '150mm ID / 175mm OD Drainage Pipe'],
    200 => ['od' => 235, 'color' => '#0ea5e9', 'label' => '200mm ID / 235mm OD Sewer Pipe'],
    250 => ['od' => 290, 'color' => '#0ea5e9', 'label' => '250mm ID / 290mm OD Drainage Pipe'],
    300 => ['od' => 350, 'color' => '#2563eb', 'label' => '300mm ID / 350mm OD Large Bore Pipe'],
    400 => ['od' => 460, 'color' => '#1d4ed8', 'label' => '400mm ID / 460mm OD Highway Culvert'],
    500 => ['od' => 580, 'color' => '#1e40af', 'label' => '500mm ID / 580mm OD Ultra-Span Culvert'],
    600 => ['od' => 700, 'color' => '#1e3a8a', 'label' => '600mm ID / 700mm OD Mega Culvert']
];

$imgDir = __DIR__;

foreach ($sizes as $id => $info) {
    $od = $info['od'];
    $color = $info['color'];
    $label = $info['label'];

    $corrugations = '';
    for ($i = 0; $i < 14; $i++) {
        $x = 100 + ($i * 32);
        $corrugations .= "<rect x='{$x}' y='110' width='16' height='180' rx='4' fill='#27272a' stroke='#3f3f46' stroke-width='2'/>";
        $corrugations .= "<rect x='".($x+16)."' y='125' width='16' height='150' fill='#18181b' />";
    }

    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 400" width="100%" height="100%">
  <defs>
    <linearGradient id="pipeGrad" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop offset="0%" stop-color="#3f3f46" />
      <stop offset="30%" stop-color="#18181b" />
      <stop offset="70%" stop-color="#09090b" />
      <stop offset="100%" stop-color="#27272a" />
    </linearGradient>
    <linearGradient id="innerBore" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop offset="0%" stop-color="{$color}" stop-opacity="0.9"/>
      <stop offset="50%" stop-color="#ffffff" stop-opacity="0.3"/>
      <stop offset="100%" stop-color="{$color}"/>
    </linearGradient>
    <filter id="shadow" x="-5%" y="-5%" width="110%" height="115%">
      <feDropShadow dx="0" dy="12" stdDeviation="10" flood-color="#000" flood-opacity="0.35"/>
    </filter>
  </defs>

  <rect width="100%" height="100%" fill="#f1f5f9" rx="16"/>

  <!-- Grid Pattern -->
  <pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse">
    <path d="M 20 0 L 0 0 0 20" fill="none" stroke="#e2e8f0" stroke-width="1"/>
  </pattern>
  <rect width="100%" height="100%" fill="url(#grid)" opacity="0.6"/>

  <!-- Pipe Body -->
  <g filter="url(#shadow)">
    <!-- Base Body -->
    <rect x="80" y="120" width="480" height="160" rx="8" fill="url(#pipeGrad)"/>

    <!-- Corrugation Ribs -->
    {$corrugations}

    <!-- Socket Joint End -->
    <path d="M 80 100 L 105 100 L 105 300 L 80 300 Z" fill="#3f3f46" stroke="#52525b" stroke-width="2"/>
    <ellipse cx="80" cy="200" rx="20" ry="100" fill="#18181b" stroke="#71717a" stroke-width="3"/>
    
    <!-- Smooth Inner Bore View -->
    <ellipse cx="80" cy="200" rx="14" ry="75" fill="url(#innerBore)"/>
    <circle cx="80" cy="200" r="10" fill="#09090b" opacity="0.8"/>

    <!-- Spigot End -->
    <ellipse cx="560" cy="200" rx="18" ry="80" fill="#27272a" stroke="#52525b" stroke-width="2"/>
  </g>

  <!-- Technical Spec Callout Badges -->
  <g transform="translate(40, 30)">
    <rect width="140" height="32" rx="16" fill="#0f172a"/>
    <text x="70" y="21" fill="#38bdf8" font-family="system-ui, sans-serif" font-size="13" font-weight="700" text-anchor="middle">IS 16098-2 / SN8</text>
  </g>

  <g transform="translate(460, 30)">
    <rect width="140" height="32" rx="16" fill="#ea580c"/>
    <text x="70" y="21" fill="#ffffff" font-family="system-ui, sans-serif" font-size="13" font-weight="700" text-anchor="middle">PE-100 VIRGIN</text>
  </g>

  <!-- Dimension Indicator -->
  <g transform="translate(80, 330)">
    <line x1="0" y1="10" x2="480" y2="10" stroke="#64748b" stroke-width="2" stroke-dasharray="4 4"/>
    <rect x="180" y="-2" width="120" height="24" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
    <text x="240" y="15" fill="#0f172a" font-family="system-ui, sans-serif" font-size="12" font-weight="600" text-anchor="middle">ID: {$id}mm | OD: {$od}mm</text>
  </g>

  <text x="320" y="380" fill="#334155" font-family="system-ui, sans-serif" font-size="14" font-weight="600" text-anchor="middle">{$label}</text>
</svg>
SVG;

    file_put_contents($imgDir . "/dwc-pipe-{$id}mm.svg", $svg);
}

// Generate Cross-Section Diagram
$crossSectionSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%">
  <rect width="100%" height="100%" fill="#0f172a" rx="16"/>
  <defs>
    <linearGradient id="metalGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#38bdf8"/>
      <stop offset="100%" stop-color="#0284c7"/>
    </linearGradient>
  </defs>

  <text x="300" y="40" fill="#ffffff" font-family="system-ui, sans-serif" font-size="18" font-weight="700" text-anchor="middle">DWC DUAL-WALL CORRUGATION PROFILE</text>
  <text x="300" y="65" fill="#94a3b8" font-family="system-ui, sans-serif" font-size="13" text-anchor="middle">Structural Wall HDPE Pipe - IS 16098 (Part 2)</text>

  <!-- Pipe Wall Profile Cutaway -->
  <g transform="translate(50, 100)">
    <!-- Corrugated Outer Wall -->
    <path d="M 0 60 C 25 10, 45 10, 70 60 C 95 10, 115 10, 140 60 C 165 10, 185 10, 210 60 C 235 10, 255 10, 280 60 C 305 10, 325 10, 350 60 C 375 10, 395 10, 420 60 C 445 10, 465 10, 490 60 L 490 80 C 465 30, 445 30, 420 80 C 395 30, 375 30, 350 80 C 325 30, 305 30, 280 80 C 255 30, 235 30, 210 80 C 185 30, 165 30, 140 80 C 115 30, 95 30, 70 80 C 45 30, 25 30, 0 80 Z" fill="#f97316"/>

    <!-- Air Gap Chambers -->
    <circle cx="70" cy="50" r="14" fill="#1e293b"/>
    <circle cx="140" cy="50" r="14" fill="#1e293b"/>
    <circle cx="210" cy="50" r="14" fill="#1e293b"/>
    <circle cx="280" cy="50" r="14" fill="#1e293b"/>
    <circle cx="350" cy="50" r="14" fill="#1e293b"/>
    <circle cx="420" cy="50" r="14" fill="#1e293b"/>

    <!-- Smooth Inner Wall -->
    <rect x="0" y="80" width="490" height="20" rx="4" fill="#38bdf8"/>

    <!-- Water Flow Line -->
    <rect x="0" y="105" width="490" height="80" fill="#0284c7" opacity="0.25"/>
    <line x1="20" y1="145" x2="470" y2="145" stroke="#38bdf8" stroke-width="3" stroke-dasharray="10 6"/>
    <text x="245" y="150" fill="#e0f2fe" font-family="system-ui, sans-serif" font-size="14" font-weight="600" text-anchor="middle">Laminar Gravity Fluid Flow (Manning's n = 0.009)</text>

    <!-- Labels & Arrows -->
    <!-- Outer wall pointer -->
    <line x1="70" y1="20" x2="70" y2="-10" stroke="#f97316" stroke-width="2"/>
    <text x="70" y="-20" fill="#fb923c" font-family="system-ui, sans-serif" font-size="12" font-weight="600" text-anchor="middle">Corrugated Outer Wall (High SN8 Stiffness)</text>

    <!-- Inner wall pointer -->
    <line x1="380" y1="90" x2="380" y2="210" stroke="#38bdf8" stroke-width="2"/>
    <text x="380" y="230" fill="#38bdf8" font-family="system-ui, sans-serif" font-size="12" font-weight="600" text-anchor="middle">Mirror-Smooth Inner Wall (Zero Silt)</text>
  </g>

  <!-- Footer Specs -->
  <g transform="translate(50, 350)">
    <text x="0" y="0" fill="#94a3b8" font-family="system-ui, sans-serif" font-size="12">✔ 100% Virgin PE-100 HDPE</text>
    <text x="180" y="0" fill="#94a3b8" font-family="system-ui, sans-serif" font-size="12">✔ Non-Conductive & Rodent Proof</text>
    <text x="370" y="0" fill="#94a3b8" font-family="system-ui, sans-serif" font-size="12">✔ 50-100 Year Design Life</text>
  </g>
</svg>
SVG;

file_put_contents($imgDir . "/dwc-cross-section.svg", $crossSectionSvg);

// Generate Logo SVG
$logoSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 280 60" width="280" height="60">
  <defs>
    <linearGradient id="logoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#ea580c"/>
      <stop offset="100%" stop-color="#f97316"/>
    </linearGradient>
  </defs>
  <!-- Pipe Icon -->
  <g transform="translate(10, 10)">
    <rect x="0" y="5" width="38" height="30" rx="4" fill="#0f172a"/>
    <rect x="8" y="0" width="8" height="40" rx="3" fill="url(#logoGrad)"/>
    <rect x="22" y="0" width="8" height="40" rx="3" fill="url(#logoGrad)"/>
    <circle cx="19" cy="20" r="5" fill="#38bdf8"/>
  </g>
  <!-- Brand Text -->
  <text x="58" y="32" font-family="system-ui, sans-serif" font-size="22" font-weight="900" fill="currentColor" letter-spacing="-0.5">ESFIELD</text>
  <text x="150" y="32" font-family="system-ui, sans-serif" font-size="22" font-weight="800" fill="#ea580c" letter-spacing="-0.5">PIPE</text>
  <text x="58" y="47" font-family="system-ui, sans-serif" font-size="9" font-weight="700" fill="#64748b" letter-spacing="1.5">DWC HDPE SOLUTIONS</text>
</svg>
SVG;

file_put_contents($imgDir . "/logo.svg", $logoSvg);
echo "SVG assets generated successfully.\n";
