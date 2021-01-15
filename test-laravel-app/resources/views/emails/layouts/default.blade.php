<?php
/**
 * @var string[] $style
 * @var string $fontFamily
 * @var string $logoUrl
 * @var string $contactUrl
 */
?>
<div style="{{ $fontFamily }} {{ $style['body'] }}">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="{{ $style['email-wrapper'] }}" align="center">
                <table width="100%" cellpadding="0" cellspacing="0">

                    <!-- Logo -->
                    <tr>
                        <td style="{{ $style['email-masthead'] }}">
                            <a style="{{ $fontFamily }} {{ $style['email-masthead_name'] }}"
                               href="{{ www_url('/') }}"
                               target="_blank">
                                <img src="<?=$logoUrl?>" alt="<?=config('app.name')?>>" width="177" />
                            </a>
                        </td>
                    </tr>

                    <!-- Email Body -->
                    <tr>
                        <td style="{{ $style['email-body'] }}" width="100%">
                            <table style="{{ $style['email-body_inner'] }}"
                                   align="center"
                                   width="570"
                                   cellpadding="0"
                                   cellspacing="0">
                                <tr>
                                    <td style="{{ $fontFamily }} {{ $style['email-body_cell'] }}">

                                        <!-- Greeting -->
                                        <h1 style="{{ $style['header-1'] }}">
                                            @if (!empty($recipient))
                                                Hi {{ is_string($recipient) ? $recipient : $recipient->username }}
                                            @endif
                                        </h1>

                                        <!-- Intro -->
                                        @foreach ($introLines as $line)
                                            <p style="{{ $style['paragraph'] }}">
                                                {!!$line !!}
                                            </p>
                                        @endforeach

                                    <!-- Boxes -->
                                        @foreach ($boxes as $box)
                                            @if (!empty($box['title']))
                                                <p style="{{ $style['box-title'] }}">{{ $box['title'] }}</p>
                                            @endif

                                            <div style="{{ $style['box'] . (!empty($box['style']) ? ' '.$style[$box['style']] : '') }}">
                                                <?php
                                                if (!empty($box['href'])) {
                                                    echo '<a href="'.$box['href'].'" style="'.$style['box-link'].'">';
                                                } else {
                                                    echo '<span style="'.$style['box-link'].'">';
                                                }
                                                ?>

                                                @if (!empty($box['preformattedContent']))
                                                    {!! $box['preformattedContent'] !!}
                                                @else
                                                    {{ $box['content'] }}
                                                @endif

                                                <?php
                                                if (!empty($box['href'])) {
                                                    echo '</a>';
                                                } else {
                                                    echo '</span>';
                                                }
                                                ?>
                                            </div>
                                        @endforeach

                                    <!-- Action Button -->
                                        @if (isset($actionText))
                                            <table style="{{ $style['body_action'] }}"
                                                   align="center"
                                                   width="100%"
                                                   cellpadding="0"
                                                   cellspacing="0">
                                                <tr>
                                                    <td align="center">
                                                        <?php
                                                        switch ($level) {
                                                            case 'success':
                                                                $actionColor = 'btn-success';
                                                                break;
                                                            case 'error':
                                                                $actionColor = 'btn-danger';
                                                                break;
                                                            default:
                                                                $actionColor = null;
                                                        }
                                                        ?>

                                                        <a href="{{ $actionUrl }}"
                                                           style="{{ $fontFamily }} {{ $style['button'] }} {{ $actionColor ? $style[$actionColor] : '' }}"
                                                           class="button"
                                                           target="_blank">
                                                            {{ $actionText }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        @else

                                        @endif

                                    <!-- Outro -->
                                        @if (!empty($outroLines))
                                            @foreach ($outroLines as $line)
                                                <p style="{{ $style['paragraph'] }}">
                                                    {{ $line }}
                                                </p>
                                            @endforeach
                                        @endif

                                    <!-- Salutation -->
                                        <p style="{{ $style['paragraph'] }}">
                                            <em>{{ !empty($signoff) ? $signoff : config('app.name') }}</em>
                                        </p>

                                        <!-- Sub Copy -->
                                        @if (isset($actionText) || empty($noFooterContact))
                                            <table style="{{ $style['body_sub'] }}">

                                                @if (isset($actionText))
                                                    <tr>
                                                        <td style="{{ $fontFamily }}">
                                                            <p style="{{ $style['paragraph-sub'] }}">
                                                                If you’re having trouble clicking the
                                                                "{{ $actionText }}" button,
                                                                copy and paste the URL below into your web
                                                                browser:
                                                            </p>

                                                            <p style="{{ $style['paragraph-sub'] }}">
                                                                <a style="{{ $style['anchor'] }}"
                                                                   href="{{ $actionUrl }}"
                                                                   target="_blank">
                                                                    {{ $actionUrl }}
                                                                </a>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                @endif

                                            </table>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td>
                            <table style="{{ $style['email-footer'] }}"
                                   align="center"
                                   width="570"
                                   cellpadding="0"
                                   cellspacing="0">
                                <tr>
                                    <td style="{{ $fontFamily }} {{ $style['email-footer_cell'] }}">
                                        @section('footer')

                                            @if (empty($noFooterContact))
                                                <p style="{{ $style['paragraph-sub'] }}">
                                                    If you would like to contact us for any reason you can
                                                    use our contact page:
                                                </p>
                                                <p style="{{ $style['paragraph-sub'] }}">
                                                    <a style="{{ $style['anchor'] }}"
                                                       href="<?=$contactUrl?>"
                                                       target="_blank">
                                                        <?=$contactUrl?>
                                                    </a>
                                                </p>
                                            @endif

                                            <p style="{{ $style['paragraph-sub'] }}">
                                                <a style="{{ $style['anchor'] }}"
                                                   href="{{ www_url('/') }}"
                                                   target="_blank">
                                                    <img src="<?=$logoUrl?>"
                                                         alt="<?=config('app_name')?>"
                                                         height="20"/>
                                                </a>
                                            </p>
                                        @show
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
