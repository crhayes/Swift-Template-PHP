<? extend('layout')  ?>

<? section('content') ?>
	<h2>Page Title</h2>
	<p>This is the title text of the page</p>
<? close() ?>

<!-- Overwrite the footer -->
<? section('footer'); ?>
	<p>This is the page-specific footer.</p>
	@parent
<? close() ?>
