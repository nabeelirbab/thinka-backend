<html>
  <body>
  <p>
    Hello {{$username}},
  </p>
  <p style="text-transform: capitalize">
    {{$notificationMessage}}
  </p>
  <a href="https://staging.thinka.io/#/branch/{{$relationId}}/t/{{$kebabStatement}}">
    <p>
      "{{$statementText}}"
    </p>
    <p style="background-color: whitesmoke">
      {{$opinionMessage}}
    </p>
  </a>
  <p style="background-color: whitesmoke">
    <small>This is an automated message. Do not reply</small>
  </p>
  </body>
</html>