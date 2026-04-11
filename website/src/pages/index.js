import clsx from 'clsx';
import Link from '@docusaurus/Link';
import useDocusaurusContext from '@docusaurus/useDocusaurusContext';
import Layout from '@theme/Layout';
import Heading from '@theme/Heading';
import styles from './index.module.css';

import HomepageFeatures from '@site/src/components/HomepageFeatures';
import BaculaWebImageUrl from '@site/static/img/bacula-web-dashboard.png';
import AboutImageUrl from '@site/static/img/undraw_Personal_goals_re_iow7.png';
import LokaliseImageUrl from '@site/static/sponsors/lokalise-logo.png';
import PackageCloudImageUrl from '@site/static/sponsors/package-cloud.png';
import JetBrainsImageUrl from '@site/static/sponsors/jetbrains-logo.png';
import DockerImageUrl from '@site/static/sponsors/docker-logo.png';

function HomepageHeader() {
    const {siteConfig} = useDocusaurusContext();
    return (
        <header className={clsx('hero hero--primary', styles.heroBanner)}>
            <div className="container">

                <div className="hero hero--primary">
                    <div className="container">
                        <div className="row">
                            <div className="col col--5">
                                <Heading as="h1" className="hero__title text--break">
                                    {siteConfig.title} - {siteConfig.tagline}
                                </Heading>
                            </div>
                            <div className="col col--7">
                                <img src={BaculaWebImageUrl} id="hero-img" alt="Bacula-Web dashboard"/>
                            </div>

                        </div>

                    </div>
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
                                Bacula-Web is an open-source web interface built to help you monitor and understand your
                                Bacula backups more easily.

                                Bacula itself is a powerful backup solution. It can handle complex infrastructures,
                                multiple devices, and large volumes of data. But it’s primarily command-line driven,
                                which means monitoring often requires digging through logs and running manual checks.

                                Bacula-Web gives you a clearer view.

                                It turns raw backup data into structured dashboards, reports, and visual insights so you
                                can quickly see what’s running, what failed, and what needs attention.


                                <b>No change to your Bacula setup. Just better visibility into what’s already there !</b>
                            </p>

                            <div className={styles.buttons} text--center>
                                <Link
                                    className="button button--primary button--lg"
                                    to="/docs">
                                    Learn more
                                </Link>
                            </div>

                        </div>
                    </div>
                </div>

                <hr/>

                <div className="row">
                    <div className={clsx('col col--8 col--offset-2')}>
                        <Heading as="h2">
                            Why backup visibility matters ?
                        </Heading>

                        <p>
                            Backups are critical infrastructure. But a backup system is only reliable if you can trust
                            it and that trust comes from visibility.
                        </p>
                        <p>
                            In many environments, these problems often go unnoticed until the moment a restore is
                            needed.
                        </p>
                        <p>
                            And that’s when it’s too late. Monitoring isn’t just about checking if a job ran
                            successfuly.
                        </p>
                        <p>
                            It’s about understanding patterns, spotting anomalies, and identifying weak points before
                            they turn into incidents.
                        </p>
                    </div>
                </div>

                <hr/>

                <div className="row">
                    <div className={clsx('col col--8 col--offset-2')}>
                        <Heading as="h2">
                            Bacula-Web gives you that visibility.
                        </Heading>
                        <p>
                            It helps you:
                        </p>

                        <ul>
                            <li>Detect failed or incomplete jobs early</li>
                            <li>Monitor storage usage trends</li>
                            <li>Track execution times and performance</li>
                            <li>Stay informed without constant manual checks</li>
                        </ul>

                        <p>Because when recovery day comes, you shouldn’t be guessing.</p>

                        <p><b>You should already know your Bacula backups are solid !</b></p>

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
                                <p>Get a clear, real-time overview of your backup environment.
                                    See the status of your jobs, recent history, volume usage, and system
                                    health <b>at a glance</b>.
                                    No need to jump between logs or run multiple commands.
                                </p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>Backup Job Monitoring</b></p>
                                <p>
                                    Track active and completed jobs with full visibility and details.
                                    Quickly identify failed jobs, review detailed logs, and check execution
                                    statistics.
                                    When something goes wrong, <b>you know exactly where to look</b>.
                                </p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>Job and Volume Reports</b></p>
                                <p>
                                    <b>Access structured reports on job status</b>, execution time, and storage
                                    usage.
                                    Understand which jobs succeed, which fail, how long they run, and how your
                                    volumes are being used including available capacity and usage trends.
                                </p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>User-friendly Interface</b></p>
                                <p>
                                    Bacula is powerful, but it was built for the terminal.

                                    Bacula-Web provides a clean, browser-based interface that <b>makes
                                    monitoring easier</b> without changing how Bacula works underneath.
                                </p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>Multi-language Support</b></p>
                                <p>Available in more than 15 languages, Bacula-Web is accessible to teams around
                                    the world.</p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>Customizable Views</b></p>
                                <p>
                                    <b>Focus on what matters to you.</b> Customize dashboards and reports to
                                    display the information relevant to your infrastructure and workflow.
                                </p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>Support any Bacula catalog Database</b></p>
                                <p>Bacula-Web retrieves data from Bacula’s director database, compatible with
                                    MySQL/MariaDB,
                                    PostgreSQL, and SQLite, and provides comprehensive reports based on it.</p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>Native authentication</b></p>
                                <p>
                                    Bacula-Web connects directly to the Bacula Director database.

                                    It supports MySQL / MariaDB, PostgreSQL and SQLite and generates detailed
                                    reports based on your existing catalog data.

                                    No data duplication. <b>No complex integration</b>.
                                </p>
                            </li>
                            <li>
                                <p className="text--uppercase"><b>Native authentication</b></p>
                                <p>
                                    Access is protected through built-in user authentication.

                                    Only authorized users can log in and view backup data, adding <b>an extra
                                    layer of security</b> to your monitoring setup and mostly to your backed-up
                                    data !
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>

                <hr/>

                <div className="row">
                    <div className={clsx('col col--8 col--offset-2')}>
                        <Heading as="h2">
                            Built for the community & Designed to run anywhere
                        </Heading>
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
                        <img style={{height: '98px'}} alt={'Docker logo'} src={DockerImageUrl}/>
                        <img style={{width: '280px'}} alt={'JetBrains logo'} src={JetBrainsImageUrl}/>
                        <img style={{width: '480px'}} alt={'PacageCloud logo'} src={PackageCloudImageUrl}/>
                        <img style={{width: '280px'}} alt={'Lokalise logo'} src={LokaliseImageUrl}/>
                    </div>
                </div>

                <p>
                    Bacula-Web is supported by companies that generously provide infrastructure and services to help
                    sustain the project. <br/><br/>

                    Their support allows the project to remain fully open source and freely accessible to the
                    community. <br/><br/>

                    We’re grateful for their contribution ❤️
                    <div className={styles.buttons} text--center>
                        <Link
                            className="button button--primary button--lg"
                            to="/docs/contribute/#donate-or-sponsor">
                            Learn how to sponsor
                        </Link>
                    </div>
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

                <div className={styles.buttons} item shadow--tl text--center>
                    <Link
                        className="button button--success button--outline button--lg"
                        to="/docs/install/getting-started/">
                        Get it running quickly and start monitoring right away
                    </Link>
                </div>
            </main>

            <hr/>

            <HomepageSponsors/>
        </Layout>
    );
}
