<?php

namespace Antriver\LaravelSiteUtils\Mail;

class MailStyles implements MailStylesInterface
{
    public function getStyles(): array
    {
        return [
            /* Layout ------------------------------ */
            'body' => 'margin: 0; padding: 0; width: 100%; background-color: #f3f5f7;',
            'email-wrapper' => 'width: 100%; margin: 0; padding: 0; background-color: #f3f5f7;',

            /* Masthead ----------------------- */
            'email-masthead' => 'padding: 25px 0; text-align: center;',
            'email-masthead_name' => 'font-size: 16px; font-weight: bold; color: #261e1c; text-decoration: none; text-shadow: 0 1px 0 white;',
            'email-body' => 'width: 100%; margin: 0; padding: 0; border-top: 1px solid #EDEFF2; border-bottom: 1px solid #EDEFF2; background-color: #FEFEFE;',
            'email-body_inner' => 'width: auto; max-width: 570px; margin: 0 auto; padding: 0;',
            'email-body_cell' => 'padding: 35px;',
            'email-footer' => 'width: auto; max-width: 570px; margin: 0 auto; padding: 0; text-align: center;',
            'email-footer_cell' => 'color: #AEAEAE; padding: 35px; text-align: center;',

            /* Body ------------------------------ */
            'body_action' => 'width: 100%; margin: 30px auto; padding: 0; text-align: center;',
            'body_sub' => 'margin-top: 25px; padding-top: 25px; border-top: 1px solid #EDEFF2;',

            /* Type ------------------------------ */
            'anchor' => 'color:#006699; text-decoration:none;',
            'header-1' => 'margin-top: 0; color: #261e1c; font-size: 19px; font-weight: bold; text-align: left;',
            'paragraph' => 'margin 15px 0; color: #757575; font-size: 16px; line-height: 1.5em;',
            'paragraph-sub' => 'margin-top: 0; color: #757575; font-size: 12px; line-height: 1.5em;',
            'paragraph-center' => 'text-align: center;',

            /* Boxes -------------------------------- */
            'box-title' => 'margin-bottom: 12px; color: #757575; font-size: 16px; line-height: 1.5em; text-align:center;',
            'box' => 'display:block; background-color:#f3f5f7; border-radius:3px; font-size: 16px; line-height: 1.5em;
              text-decoration:none; padding:1px 12px; color: #261e1c;',
            'box-link' => 'text-decoration:none; color: #261e1c;',

            'achievement-box' => 'background:none; padding:12px; text-align:center;',
            'user-box' => 'background:none; padding:12px; text-decoration:none; color:#006699; text-align:center;',

            /* Buttons ------------------------------ */
            'button' => 'display: block; display: inline-block; padding: 8px 30px; line-height:22px;
                 background-color: #006699; border-radius: 3px; color: #ffffff; font-size: 14px;
                 text-align: center; text-decoration: none; text-transform:uppercase; -webkit-text-size-adjust: none;',
            'btn-success' => 'background-color: #388e3c;',
            'btn-danger' => 'background-color: #d32f2f;',
        ];
    }

    public function getFontFamily(): string
    {
        return 'font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;';
    }

    public function getContactUrl(): string
    {
        return ''; // TODO
    }

    public function getLogoUrl(): string
    {
        return ''; // TODO
    }
}
