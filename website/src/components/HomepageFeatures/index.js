import clsx from 'clsx';
import Heading from '@theme/Heading';
import styles from './styles.module.css';

const FeatureList = [
    {
        title: 'Open source',
        Svg: require('@site/static/img/undraw_open_source.svg').default,
        description: (
            <>
                Bacula-Web is free software released under the term of GPL license
            </>
        ),
    },
    {
        title: 'Advantages',
        Svg: require('@site/static/img/undraw_feeling_proud_qne1.svg').default,
        description: (
            <>
                <ul>
                    <li>Open source</li>
                    <li>User friendly</li>
                    <li>Translated in more than 10 languages</li>
                    <li>Easy to setup and maintain</li>
                    <li>100% web based</li>
                    <li>Safe and secure</li>
                    <li>Do not alter your Bacula catalog</li>
                </ul>
            </>
        ),
    },
    {
        title: 'Run it anywhere',
        Svg: require('@site/static/img/undraw_applications_vaxx.svg').default,
        description: (
            <>
                Bacula-Web can be installed on any rpm or deb based OS such as
                <ul>
                    <li>any rpm or deb based Linux distribution</li>
                    <li>Any *BSD like FreeBSD</li>
                    <li>Gentoo</li>
                </ul>

                Using Nginx, Apache or Lighttpd web server.

                You can also run it in a &nbsp;
                <a
                    href={'https://hub.docker.com/r/baculaweb/bacula-web'}
                    target={'_blank'} rel="noopener noreferrer"
                >
                    Docker container
                </a>
            </>
        ),
    },
    {
        title: 'How to get help',
        Svg: require('@site/static/img/undraw_different_love_a-3-rg.svg').default,
        description: (
            <>
                Bugs and feature requests can be created on the <a target={'_blank'} href={'https://github.com/bacula-web/bacula-web/issues'}>GitHub project</a>.

                Please have a look at the <a target={'_blank'} href={'https://docs.bacula-web.org/en/latest/03_get-help/support.html#bug-report-guideline'}>bugs and features request guideline</a> first.
            </>
        )
    },
    {
        title: 'Community',
        Svg: require('@site/static/img/undraw_love_it_heart_dxlp.svg').default,
        description: (
            <>
                <p>Don't be shy, start a discussion or ask a question on <a href={'https://github.com/bacula-web/bacula-web/discussions'}>Bacula-Web GitHub project</a></p>

                <p>
                    You can follow Bacula-Web project on <a href={'https://github.com/bacula-web/bacula-web'}>GitHub</a>, or <a
                    href={'https://twitter.com/BaculaWeb'}>X (Twitter)</a>
                </p>
            </>
        )
    },
    {
        title: 'Contribute and support',
        Svg: require('@site/static/img/undraw_collaborators_re_hont.svg').default,
        description: (
            <>
                <p>
                We accept any contributions from the community, for further details, please check the <a href={'https://docs.bacula-web.org/en/latest/04_contribute/index.html'}
                                                                                                         target={'_blank'}>contributor guide</a>
                </p>
            </>
        )
    }
];

function Feature({
Svg, title, description}) {
    return (
        <div className={clsx('col col--4')}>
            <div className="text--center">
                <Svg className={styles.featureSvg} role="img"/>
            </div>
            <div className="text--center padding-horiz--md">
                <Heading as="h2">{title}</Heading>
                <p>{description}</p>
            </div>
        </div>
    );
}

export default function HomepageFeatures() {
    return (
        <section className={styles.features}>
            <div className="container">
                <div className="row">
                    {FeatureList.map((props, idx) => (
                        <Feature key={idx} {...props} />
                    ))}
                </div>
            </div>
        </section>
    );
}