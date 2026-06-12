export default {
    customSidebar: [
        {
            type: 'doc',
            id: 'introduction',
            label: 'Introduction',
        },
        {
            type: 'category',
            label: 'Getting started',
            collapsed: true,
            items: [
                /* 'install/getting-started', */
                {
                    type: 'category',
                    label: 'Install',
                    collapsed: true,
                    link: {
                        type: 'doc',
                        id: 'install/getting-started',
                    },

                    items: [
                        'install/requirements',
                        {
                            type: 'category',
                            label: 'Web server setup',
                            items: [
                                'install/php/index',
                                'install/web-server/apache',
                                'install/web-server/nginx',
                                'install/web-server/lighttpd',
                                "install/web-server/shared-web-server"
                            ],
                        },
                        {
                            type: 'category',
                            label: 'Installation options',
                            link: {
                                type: 'generated-index',
                                title: 'Installation options',
                            },
                            items: [
                                "install/archive-install",
                                "install/composer-install",
                                "install/docker-install",
                                "install/source-install"
                            ],
                        },
                        'install/configure',
                        'install/setup-user-auth',
                        'install/selinux',
                    ]
                },
                'install/upgrade',
                'install/test'
            ]
        },
        {
            type: 'category',
            label: 'Admin guides',
            link: {
                type: 'generated-index',
                title: 'Admin guides',
                description: 'This section contains various administration guides',
            },
            collapsed: true,
            items: [
                'admin-guides/permissions-and-ownership',
                'admin-guides/bacula-database-user',
                'admin-guides/users-management',
                'admin-guides/user-settings',
                'admin-guides/references'
            ]
        },
        "gethelp/index",
        {
            "type": "category",
            "label": "Contributing",
            "collapsed": true,
            "items": [
                "contribute/index",
                "contribute/reporting-issue-guideline",
                "contribute/source-code",
                "contribute/translations"
            ]
        },
        "faq",
        {
            type: 'category',
            label: 'About',
            link: {
                type: 'generated-index',
            },
            collapsed: true,
            items: [
                'about/index',
                'about/features',
                'about/license',
                'about/security',
                'about/releases'
            ]
        },
        {
            type: 'category',
            label: 'Contributing',
            collapsed: true,
            items: [
                'contribute/index',
                "contribute/reporting-issue-guideline",
                'contribute/source-code',
                'contribute/translations'
            ],
        },
        {
            type: 'link',
            label: 'Releases', // The link label
            href: 'https://github.com/bacula-web/bacula-web/releases'
        },
    ],
};