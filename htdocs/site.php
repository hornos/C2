<!--TODO: auth-->
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

  <div class="header">
  C2 - user -
  <select name="menu"></select>
  <input type="button" class="exit" name="logout" id="logout" value=" Logout ">
  </div>
  <script>
    $('#logout').click( function() {
      _post( { 'c' : 'logout' }, _cb_logout );
      return true;
    });
  </script>

  <div>
  PAGE
  </div>
</body>

</html>
