<html>
  <head>
    <script language="javascript"><!--

      if (self.location != top.location)
      {
        // move to end of order in top frame
        if (document.images)
        {
          top.location.replace("<?php echo url_for("@sf_sagepay_endurl", true); ?>");
        } else
        {
          top.location.href = "<?php echo url_for("@sf_sagepay_endurl", true); ?>";
        }
      }

    //--></script>
    <style type="text/css">
      body
      {
        font-family: Arial, Helvetica, clean, serif;
        font-size: 0.875em;
      }
    </style>
  </head>
  <body>
    Please wait... <a href="<?php echo url_for("@sf_sagepay_endurl", true); ?>">or click here</a> to proceed.
  </body>
</html>
