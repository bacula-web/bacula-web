import clsx from 'clsx';
import Link from '@docusaurus/Link';
import useDocusaurusContext from '@docusaurus/useDocusaurusContext';
import Layout from '@theme/Layout';
import Heading from '@theme/Heading';
import styles from './index.module.css';

import HomepageFeatures from '@site/src/components/HomepageFeatures';
import BaculaWebImageUrl from '@site/static/img/bacula-web-dashboard.png';
import AboutImageUrl from '@site/static/img/undraw_Personal_goals_re_iow7.png';
import TravisImageUrl from '@site/static/sponsors/travis-ci-logo.png';
import LokaliseImageUrl from '@site/static/sponsors/lokalise-logo-colour-black-text.png';
import PackageCloudImageUrl from '@site/static/sponsors/package-cloud.png';
import JetBrainsImageUrl from '@site/static/sponsors/jetbrains-logo.png';

function HomepageHeader() {
    const {siteConfig} = useDocusaurusContext();
    return (
        <header className={clsx('hero hero--primary', styles.heroBanner)}>
            <div className="container">
                <img alt="bacula-web dashboard" id="hero-img" src={BaculaWebImageUrl}/>
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

function HomepageBody() {
    return (
        <section>
            <div className="container">
                <div className="row">
                    <div className={clsx('col col--6')}>
                        <img alt={'about'} src={AboutImageUrl}/>
                    </div>
                    <div className={clsx('col col--6')}>
                        <div className="text--center">
                            <Heading as="h2" className="margin-top--xl">What is Bacula-Web ?</Heading>
                            <p>
                                Bacula-Web is an open-source, web-based tool designed to provide a user-friendly
                                graphical
                                interface for managing and monitoring <a href="https://www.bacula.org/"
                                                                         target="_blank">Bacula</a> backups.
                                <br/><br/>
                                <a href="https://www.bacula.org/" target="_blank">Bacula</a> is a powerful, open-source
                                backup solution for
                                networks that allows the backup,
                                recovery, and verification of data across a range of computers and devices.
                                <br/><br/>
                                While <a href="https://www.bacula.org/" target="_blank">Bacula</a> itself is
                                command-line
                                driven, Bacula-Web offers a more accessible way to visualize Bacula activities and
                                system health.
                            </p>
                        </div>
                    </div>
                </div>

                <hr/>

                <div className="row">
                    <div className={clsx('col col--8 col--offset-2')}>
                        <Heading as="h2">
                            Key Bacula-Web features
                        </Heading>

                        <ul>
                            <li>
                                <p className="text--uppercase"><b>Dashboard Overview</b></p>
                                <p>Displays real-time information on the status of backup jobs, job history, volume
                                    usage, and system health metrics, giving administrators a comprehensive view of
                                    the backup environment at a glance.</p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>Backup Job Monitoring</b></p>
                                <p>Allows users to track the progress and results of both active and completed backup
                                    jobs, including detailed logs, errors, and backup statistics.</p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>Job and Volume Reports</b></p>
                                <p>Offers a wide range of reports, such as job status (successful, failed, etc.),
                                    backup job execution time, and detailed volume information (e.g., available
                                    capacity, usage).
                                </p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>User-friendly Interface</b></p>
                                <p>Makes it easier for administrators to navigate and manage Bacula’s features without
                                    requiring deep knowledge of command-line operations.</p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>Multi-language Support</b></p>
                                <p>Bacula-Web supports 15 languages, making it accessible to a global user base.</p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>Customizable Views</b></p>
                                <p>Users can customize the interface to show relevant information and statistics based
                                    on their needs.</p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>Support any Bacula catalog Database</b></p>
                                <p>Bacula-Web retrieves data from Bacula’s director database, compatible with MySQL,
                                    PostgreSQL, and SQLite, and provides comprehensive reports based on it.</p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>Native authentication</b></p>
                                <p>Bacula-Web includes a simple user authentication system that requires users to log
                                    in before accessing the interface. This feature provides an additional layer of
                                    security to ensure that only authorized users can view or manage Bacula backup
                                    data.</p>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </section>
    )
        ;
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
            <HomepageBody/>
            <main>
                <HomepageFeatures/>
            </main>
            <HomepageSponsors/>
        </Layout>
    );
}
