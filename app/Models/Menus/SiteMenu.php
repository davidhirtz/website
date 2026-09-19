<?php

declare(strict_types=1);

namespace App\Models\Menus;

use Hirtz\Cms\Models\Menus\Menu;

/**
 * The menus `config/prod.php` declares, so neither the declaration nor the layout addresses one by a bare integer.
 * The two cases are the values `Cms\Migrations\M260915200000MenuIds` writes for a v2 `show_in_menu` and
 * `show_in_footer`.
 *
 * @see Menu::getValue()
 */
enum SiteMenu: int
{
    case Main = 1;
    case Footer = 2;
}
