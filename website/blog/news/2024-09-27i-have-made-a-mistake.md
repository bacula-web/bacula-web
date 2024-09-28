---
title: "I have made a mistake"
description: "I have made a mistake"
date: 'Sat, 28 Sep 2024 07:20:39 +0200'
tags:
  - news
authors: dfranco
---

Oups ... I think I've made a mistake ... 🙄

![](https://i.imgur.com/RgHzxTR.jpeg)

<!--truncate-->

## What's wrong?

Since I started working on the next major release (10.0.0), I created another Git branch

Up to now, I've tried to apply a simplified git branching strategy inspired by [git-flow](https://github.com/nvie/gitflow). 
Which means that the default branch (master) should, at least contain stable code followed by a new release.

But a few weeks ago, by mistake, I thought it would be a good idea to merge the 10.x development branch into master,

I thought it would take me a few days or 1-2 weeks and then 10.0.0 will be ready for release, but I know as of today, it will take another bunch of weeks.

And that was my mistake ...

## Why this was a mistake?

There are a few security issues with some Bacula-Web dependencies I would like to fix and publish a **security release** within the next few days, 
Still, as mentioned above, I'm somehow stuck with current commits in the master branch.

Why? As mentioned above, the current master branch contains commits from the 10.0.0 development branch.
Publishing a beta release of 10.0.0 with the current state of the code is not even mature and ready yet, won't be fair.

So now I have to make some decisions regarding commits from the current master branch ...

## Remediation

Here's how I'm going to solve this problem and release a new version of the project.

* reset the [master branch](https://github.com/bacula-web/bacula-web) to the tag [v9.5.1](https://github.com/bacula-web/bacula-web/tree/4c68fcfb38c859ba72c60c36597ca8c160ec529f)
* create two different branches
  * 9.x development branch
  * 10.x development branch

For those who have forked the [master branch](https://github.com/bacula-web/bacula-web) after the 9.5.1 release commit,
you can fix your local master branch by running.

```shell
$ git checkout master
$ git reset --hard 4c68fcfb
```

## Future Git branch strategy

Development for major version 9 will be done in branch 9.x, while I'll keep working on branch 10.x 

The contribution guide will be updated according to these changes.

Shall you have any comments or remarks, feel free to start a new thread in the [GitHub discussions](https://github.com/bacula-web/bacula-web/discussions)

I hope you enjoy the efforts I put into this project, and I would like to thank you all for using and supporting the Bacula-Web project.

Best,

Davide
