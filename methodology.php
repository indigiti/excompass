<?php
declare(strict_types=1);
require __DIR__.'/runtime.php';
page_header('How ExCompass ranks');
?>
<main id="content" class="shell section prose">
  <span class="eyebrow">Methodology</span><h1>A ranking should explain itself.</h1><p class="lead">ExCompass is designed around an evidence → criterion score → weighted total → editorial review → published ranking chain. The current dataset is deliberately dummy content so the complete product workflow can be developed without waiting for production research.</p>
  <div class="method-grid" data-reveal>
    <section><b>01</b><h2>Evidence</h2><p>Verified source material and structured attributes attach to each entity.</p></section>
    <section><b>02</b><h2>Scoring</h2><p>Vertical-specific criteria convert evidence into transparent, weighted signals.</p></section>
    <section><b>03</b><h2>Review</h2><p>An editor reviews evidence quality, conflicts, recency and justified overrides.</p></section>
    <section><b>04</b><h2>Version</h2><p>Published ranking snapshots remain immutable, explainable and reproducible.</p></section>
  </div>
  <div class="separation" data-reveal><span class="eyebrow">Non-negotiable</span><h2>Editorial ≠ commercial</h2><p>Featured and Sponsored presentation may affect inventory or visibility, but they must never modify the underlying ranking score.</p></div>
</main>
<?php page_footer(); ?>
