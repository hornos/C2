<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>C2 Demo</title>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
</head>

<body>
  <!-- libs -->
  <script src="lib/c2/c2.js"></script>
  <link href="lib/c2/c2.css" rel="stylesheet" type="text/css">

  <div class="login">
  <form id="login" name="login" action="javascript:return true;">
    <!-- public -->
    <div class="field">
    <span>User:</span>
    <span><input type="text" id="u" name="u" /></span>
    </div>

    <div class="field">
    <span>Pass:</span>
    <span><input type="password" id="p" name="p" />
    <input type="button" id="s" name="s" class="submit" value=" Login " />
    </span>
    </div>
  </form>
  </div>
  <script>
    $('#login').submit( function() {
      u = $('#u').val();
      p = $('#p').val();
      var pl = { 'u' : u, 'p' : p, 'c' : 'login' };
      _post( pl, _cb_login );
      return true;
    });
    $('#s').click( function() {
      return $('#login').submit();
    });
  </script>
</body>

</html>
