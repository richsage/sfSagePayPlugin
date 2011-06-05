<script language="javascript"><!--

window.onload = function()
{
  document.fm3d.submit();
}

//--></script>

<form name="fm3d" method="post" action="<?php echo $ACSURL; ?>">
  <input type="hidden" name="PaReq" value="<?php echo $PAReq; ?>" />
  <input type="hidden" name="TermUrl" value="<?php echo url_for("@sf_sagepay_3dresult", true); ?>" />
  <input type="hidden" name="MD" value="<?php echo $MD; ?>" />
  <noscript>
    <!-- fallback for non-Javascript -->
    <input type="submit" value="Proceed" />
  </noscript>
</form>

