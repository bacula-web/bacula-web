import clsx from 'clsx';
import Heading from '@theme/Heading';
import styles from './styles.module.css';
import Link from "@docusaurus/Link";

const FeatureList = [
    {
        title: 'Open source',
        Svg: require('@site/static/img/undraw_open_source.svg').default,
        description: (
            <>
                Bacula-Web is fully open source and released under the GPL version 2 license. <br/><br/>
                You can use it, modify it, and contribute to it freely. <br/><br/>
                No hidden layers. No proprietary lock-in. And it will remain like this FOREVER (no Premium plan in the future, I promise).
            </>
        ),
    },
    {
        title: 'Why teams choose Bacula-Web',
        Svg: require('@site/static/img/undraw_feeling_proud_qne1.svg').default,
        description: (
            <>
                <ul>
                    <li>Clean, responsive and user-friendly interface</li>
                    <li>Available in more than 15+ languages</li>
                    <li>Easy to install and maintain</li>
                    <li>100% web-based</li>
                    <li>Secure access with user authentication</li>
                    <li>Does not modify your Bacula catalog</li>
                    <li>Monitor several Bacula directory from one place</li>
                </ul>

                <p>Bacula-Web works alongside your existing setup. It adds visibility, not complexity.</p>
            </>
        ),
    },
    {
        title: 'Run it anywhere',
        Svg: require('@site/static/img/undraw_applications_vaxx.svg').default,
        description: (
            <>
                <p>Bacula-Web is lightweight and flexible. You can install it on:</p>

                <ul>
                    <li><b>Any</b> Linux distribution or *BSD like FreeBSD</li>
                    <li>macOs</li>
                </ul>

                It can be run using the following web server : Nginx, Apache and Lighttpd.<br/><br/>

                Or you can deploy it inside a <a href="https://hub.docker.com/r/baculaweb/bacula-web" target="_blank">Docker container</a>. <br/><br/>

                No exotic requirements. No heavy dependencies.
            </>
        ),
    },
    {
        title: 'How to get help',
        Svg: require('@site/static/img/undraw_different_love_a-3-rg.svg').default,
        description: (
            <>
                Found a bug? Have a feature request? <br/>

                You can open an issue directly on the <a href={'https://github.com/bacula-web/bacula-web/issues'} target={"_blank"}>GitHub project</a>.<br/><br/>

                <i>Before submitting, please review the <a href={'/docs/contribute/reporting-issue-guideline'} target={'_blank'}>contribution and reporting guidelines</a> to help us keep things organized and efficient.</i>
            </>
        )
    },
    {
        title: 'Join the Community',
        Svg: require('@site/static/img/undraw_love_it_heart_dxlp.svg').default,
        description: (
            <>
                Bacula-Web is driven by its users. Start a discussion, ask a question, or <a href={"https://github.com/bacula-web/bacula-web/discussions"}>share feedback on GitHub.</a> <br/><br/>

                You can also follow the project on :

                <ul>
                    <li>
                        <a href={'https://github.com/bacula-web/bacula-web'}>GitHub</a>
                    </li>
                    <li>
                        <a href={'https://www.youtube.com/@Bacula-Web'}>Youtube</a>
                    </li>
                    <li>
                        <a href={'https://x.com/BaculaWeb'}>X (formerly Twitter)</a>
                    </li>
                </ul>
            </>
        )
    },
    {
        title: 'Contribute and support',
        Svg: require('@site/static/img/undraw_collaborators_re_hont.svg').default,
        description: (
            <>
                We welcome contributions from the community. Whether it’s code, documentation improvements, translations, or ideas, every contribution helps move the project forward.<br/><br/>

                If you’d like to contribute, please check the <a href={'/docs/contribute/'}>contributor guide</a> to get started.<br/><br/>

            </>
        )
    }
];

function Line({}) {
    return (
        <div className={clsx('col col-xs-12')}>
            <hr />
        </div>
    );
}

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
                <Line></Line>
                <div className="row">
                    {FeatureList.map((props, idx) => (
                        <Feature key={idx} {...props} />
                    ))}
                </div>
            </div>
        </section>
    );
}