import clsx from 'clsx';
import Link from '@docusaurus/Link';
import useDocusaurusContext from '@docusaurus/useDocusaurusContext';
import Layout from '@theme/Layout';
import HomepageFeatures from '@site/src/components/HomepageFeatures';

import Heading from '@theme/Heading';
import styles from './index.module.css';

import BaculaWebImageUrl from '@site/static/img/bacula-web-dashboard.png';

import TravisImageUrl from '@site/static/sponsors/travis-ci-logo.png';
import LokaliseImageUrl from '@site/static/sponsors/lokalise_logo_colour_black_text.png';
import PackageCloudImageUrl from '@site/static/sponsors/package-cloud.png';
import JetBrainsImageUrl from '@site/static/sponsors/jetbrains-logo.png';

function HomepageHeader() {
    const {siteConfig} = useDocusaurusContext();
    return (
        <header className={clsx('hero hero--primary', styles.heroBanner)}>
            <div className="container">
                <img id="hero-img" src={BaculaWebImageUrl}/>
                <Heading as="h1" className="hero__title">
                    {siteConfig.title}
                </Heading>
                <p className="hero__subtitle text--center">{siteConfig.tagline}</p>
                <div className={styles.buttons}>
                    <Link
                        className="button button--secondary button--lg"
                        to="https://docs.bacula-web.org">
                        Learn more
                    </Link>
                </div>
            </div>
        </header>
    );
}

function HomepageSponsors() {
    return (
        <section className={styles.features}>
            <div className="container">
                <Heading as="h2">Sponsors</Heading>

                <p>
                    You can find below a list of companies who provide infrastructure and services for free to the open
                    source Bacula-Web project.
                </p>

                <div className={'container'}>
                    <div className="row sponsors">
                        <img style={{width: '280px'}} alt={'JetBrains logo'} src={JetBrainsImageUrl}/>
                        <img style={{width: '480px'}} alt={'PacageCloud logo'} src={PackageCloudImageUrl}/>
                        <img style={{width: '280px'}} alt={'Travis CI logo'} src={TravisImageUrl}/>
                        <img style={{width: '280px'}} alt={'Lokalise logo'} src={LokaliseImageUrl}/>
                    </div>
                </div>

                <p>
                    Again, a big THANKS to the sponsors listed above for their support on Open Source projects! :heart:
                </p>
            </div>
        </section>
    );
}

export default function Home() {
    const {siteConfig} = useDocusaurusContext();
    return (
        <Layout
            title={`Welcome to ${siteConfig.title} project`}
            description="Open Source monitoring and reporting tool for Bacula">
            <HomepageHeader/>
            <main>
                <HomepageFeatures/>
            </main>
            <HomepageSponsors/>
        </Layout>
    );
}
