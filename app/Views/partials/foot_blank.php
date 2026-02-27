<footer class="pt-3">
	<div class="text-center px-3">
        Copyright © <?=date('Y');?> <?=ucwords(strtolower($set->nama))?>
	</div>
</footer>

<input type="hidden" id="names" value="<?= csrf_token() ?>">
<input type="hidden" id="tokens" value="<?= csrf_hash() ?>">

<script>
	function updateToken(token){
			$("#tokens,.tokens").val(token);
		}
</script>