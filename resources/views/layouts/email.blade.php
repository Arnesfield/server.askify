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
        {{ $attrDate }}
        alt="logo"
        src="{{ $config['logo'] }}"
        style="vertical-align: middle; display: inline-block; width: 32px; height: 32px; font-size: 8px"
      />

      <span
        {{ $attrDate }}
        style="vertical-align: middle;"
      >{{ $config['app_name'] }}</span>
    </h1>
  </thead>

  <tbody>
    <h3 {{ $attrDate }}>@yield('title')</h3>

    <p>Dear
      <strong
        {{ $attrDate }}
        style="color: {{ $config['color_primary'] }};"
      >{{ $user->fullname }}</strong>,
    </p>

    <p {{ $attrDate }}>@yield('content')</p>

    @yield('append')
  </tbody>
  
  <tfoot>
    <div style="padding-top: 24px;">
      <small>
        <p style="color: #757575" {{ $attrDate }}>
          Email sent to
          <span
            {{ $attrDate }}
            style="color: {{ $config['color_accent'] }}; text-decoration: underline"
          >{{ $user->email }}</span>
          on <strong>{{ $date }}</strong>.

          <br/>

          &copy; 2019. {{ $config['app_name'] }}.
        </p>
      </small>
    </div>
  </tfoot>
</table>
