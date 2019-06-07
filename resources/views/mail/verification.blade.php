<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Sans"/>
<style>
body {
  font-family: 'Noto Sans', sans-serif;
}
</style>

<table>
  <thead>
    <h1 style="color: {{ $config['color_primary'] }};">
      <img
        data-time="{{ $date }}"
        src="{{ $config['logo'] }}"
        style="vertical-align: middle; display: inline-block; width: 32px; height: 32px"
      />

      <span style="vertical-align: middle;">{{ $config['app_name'] }}
        {{ $hiddenDate }}
      </span>

      {{ $hiddenDate }}
    </h1>
  </thead>

  <tbody>
    <h3>Email Verification
      {{ $hiddenDate }}
    </h3>

    <p>Dear <strong style="color: {{ $config['color_primary'] }};">{{ $user['fullname'] }}</strong>,
      {{ $hiddenDate }}
    </p>

    <p>
      We need you to confirm this email address in order to get started with exploring Askify. Email confirmation is simple and fast, just click on the link below to complete this process.
      {{ $hiddenDate }}
    </p>

    <a
      style="color: {{ $config['color_accent'] }}"
      href="{{ url('auth/verify?c=' . $code) }}"
    >Verify email address
      {{ $hiddenDate }}
    </a>
  </tbody>
  
  <tfoot>
    <div style="padding-top: 24px;">
      <small>
        <p style="color: #757575">
          Email sent to
          <span
            style="color: {{ $config['color_accent'] }}; text-decoration: underline"
          >{{ $user['email'] }}</span>
          on <strong>{{ $date }}</strong>.

          <br>

          &copy; 2019. {{ $config['app_name'] }}.
          {{ $hiddenDate }}
        </p>
      </small>
    </div>
  </tfoot>
</table>
