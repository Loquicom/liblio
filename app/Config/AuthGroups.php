<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'manager';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://codeigniter4.github.io/shield/quick_start_guide/using_authorization/#change-available-groups for more info
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Complete control of the site.',
        ],
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Day to day administrators of the site.',
        ],
        'manager' => [
            'title'       => 'Manager',
            'description' => 'Backend (internal) users of the site.',
        ],
        'member' => [
            'title'       => 'Member',
            'description' => 'General (external) users of the site.',
        ],
        'beta' => [
            'title'       => 'Beta User',
            'description' => 'Has access to beta-level features.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     *
     * If a permission is not listed here it cannot be used.
     */
    public array $permissions = [
        'manage.books'       => 'Can edit books informations (anybody can view)',
        'manage.members'     => 'Can view and edit members informations',
        'manage.borrow'      => 'Possibility of borrowing books',
        'manage.return'      => 'Possibility of returning books',
        'manage.authors'     => 'Can view and edit authors informations',
        'config.users.admin' => 'Can manage admin users',
        'config.users.view'  => 'Can view users informations',
        'config.users.edit'  => 'Can edit users informations',
        'config.publisher'   => 'Manage publisher',
        'config.website'     => 'Can configure website parameters',
        'config.export'      => 'Can export data to CSV',
        'beta.access'        => 'Can access beta-level features',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines group-level permissions.
     */
    public array $matrix = [
        'superadmin' => [
            'manage.*',
            'config.*',
        ],
        'admin' => [
            'manage.*',
            'config.users.view',
            'config.users.edit',
            'config.publisher',
            'config.export',
        ],
        'manager' => [
            'manage.*',
            'config.publisher',
            'config.users.view',
        ],
        'member' => [],
        'beta' => [
            'beta.access',
        ],
    ];
}
