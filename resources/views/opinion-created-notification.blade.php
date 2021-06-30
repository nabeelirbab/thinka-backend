<html>
  <body>
  <p>
    Hello {{$username}},
  </p>
  <p >
    {{$notificationMessage}}
  </p>
  
  <p>
    <a href="https://staging.thinka.io/#/branch/{{$relationId}}/t/{{$kebabStatement}}">
      "{{$statementText}}"
    </a>
  </p>
  <p>
    {!! $opinionMessage !!}
  </p>
  <hr />
  <p style="background-color: whitesmoke">
    <small>This is an automated message. Do not reply</small>
  </p>
  </body>
</html>