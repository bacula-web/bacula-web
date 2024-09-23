// @ts-check
// `@type` JSDoc annotations allow editor autocompletion and type checking
// (when paired with `@ts-check`).
// There are various equivalent ways to declare your Docusaurus config.
// See: https://docusaurus.io/docs/api/docusaurus-config

import {themes as prismThemes} from 'prism-react-renderer';

/** @type {import('@docusaurus/types').Config} */
const config = {
    title: 'Bacula-Web',
    tagline: 'Bacula monitoring and reporting made easy',
    favicon: 'favicons/favicon.ico',

    // Set the production url of your site here
    url: 'https://www.bacula-web.org',
    // Set the /<baseUrl>/ pathname under which your site is served
    // For GitHub pages deployment, it is often '/<projectName>/'
    baseUrl: '/',

    // GitHub pages deployment config.
    // If you aren't using GitHub pages, you don't need these.
    organizationName: 'bacula-web', // Usually your GitHub org/user name.
    projectName: 'bacula-web', // Usually your repo name.

    onBrokenLinks: 'throw',
    onBrokenMarkdownLinks: 'warn',

    // Even if you don't use internationalization, you can use this field to set
    // useful metadata like html lang. For example, if your site is Chinese, you
    // may want to replace "en" with "zh-Hans".
    i18n: {
        defaultLocale: 'en',
        locales: ['en'],
    },

    presets: [
        [
            'classic',
            /** @type {import('@docusaurus/preset-classic').Options} */
            ({
                docs: {
                    sidebarPath: './sidebars.js',

                    // Please change this to your repo.
                    // Remove this to remove the "edit this page" links.
                    // Disabled for the moment
                    // editUrl:
                    //  'https://github.com/facebook/docusaurus/tree/main/packages/create-docusaurus/templates/shared/',
                },
                blog: {
                    showReadingTime: true,
                    feedOptions: {
                        type: ['rss', 'atom'],
                        xslt: true,
                    },
                    showLastUpdateTime: true,
                    showLastUpdateAuthor: true,
                    // Please change this to your repo.
                    // Remove this to remove the "edit this page" links.
                    /*
                              editUrl:
                                'https://github.com/facebook/docusaurus/tree/main/packages/create-docusaurus/templates/shared/',
                    */
                    // Useful options to enforce blogging best practices
                    onInlineTags: 'warn',
                    onInlineAuthors: 'warn',
                    onUntruncatedBlogPosts: 'warn',
                    blogTitle: 'News and Releases',
                    blogDescription: 'Bacula-Web latest news, announcements and releases',
                    postsPerPage: 5,
                    blogSidebarCount: 'ALL'
                },
                theme: {
                    customCss: './src/css/custom.css',
                },
            }),
        ],
    ],

    themeConfig:
    /** @type {import('@docusaurus/preset-classic').ThemeConfig} */
        ({
            matomo: {
                matomoUrl: 'https://baculaweb.matomo.cloud/',
                siteId: '1',
                phpLoader: 'matomo.php',
                jsLoader: 'matomo.js',
            },
            // Replace with your project's social card
            image: 'img/bacula-web-social-card.jpg',
            metadata: [
                {name: 'keywords', content: 'bacula, monitoring, reporting. opensource'},
                {name: 'twittercard', content: 'img/bacula-web-social-card.jpg'}
            ],
            navbar: {
                title: 'Bacula-Web',
                logo: {
                    alt: 'Bacula-Web logo',
                    src: 'img/bacula-web-logo.png',
                },
                items: [
                    /*
                    {
                        type: 'docSidebar',
                        sidebarId: 'tutorialSidebar',
                        position: 'left',
                        label: 'Tutorial',
                    },
                    */
                    {
                        to: '/blog',
                        label: 'Blog',
                        position: 'left'
                    },
                    {
                        href: 'https://github.com/bacula-web/bacula-web',
                        label: 'GitHub',
                        position: 'right',
                    },
                ],
            },
            footer: {
                style: 'dark',
                links: [
                    {
                        title: 'Docs',
                        items: [
                            {
                                label: 'Documentation',
                                to: 'https://docs.bacula-web.org',
                            },
                        ],
                    },
                    {
                        title: 'Community',
                        items: [
                            {
                                label: 'GitHub discussions',
                                href: 'https://github.com/bacula-web/bacula-web/discussions',
                            },
                            {
                                label: 'X (Twitter)',
                                href: 'https://twitter.com/BaculaWeb',
                            }
                            /*
                                          Uncomment once the YT channel has some content
                                          {
                                            label: 'Youtube',
                                            href: 'https://youtube.com/@bacula-web?feature=shared',
                                          }
                            */
                        ],
                    },
                    {
                        title: 'More',
                        items: [
                            {
                                label: 'Blog',
                                to: '/blog',
                            },
                            {
                                label: 'GitHub',
                                href: 'https://github.com/bacula-web/bacula-web',
                            },],
                    },
                ],
                copyright: `Copyright © ${new Date().getFullYear()} Davide Franco`,
            },
            prism: {
                theme: prismThemes.github,
                darkTheme: prismThemes.dracula,
            },
            announcementBar: {
                id: 'website_under_construction',
                content:
                    '<b>Bacula-Web website and its documentation are actively being updated.</b>',
                backgroundColor: '#000000',
                textColor: '#cacaca',
                isCloseable: false,
            },
        }),
    plugins: [
        require.resolve('docusaurus-lunr-search'),
        'docusaurus-plugin-matomo'
    ],
};

export default config;
