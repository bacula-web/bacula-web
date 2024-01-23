<?php

declare(strict_types=1);

/**
 * Copyright (C) 2010-present Davide Franco
 *
 * This file is part of Bacula-Web.
 *
 * Bacula-Web is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Bacula-Web is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with Bacula-Web. If not, see
 * <https://www.gnu.org/licenses/>.
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PublishAssetsCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Publish web assets')
            ->setHelp('Publish css, javascript, etc. web resources in public folder')
            ->setHidden()
            ->setName('publishAssets');

        parent::configure();
    }

    /**
     * @param OutputInterface $output
     * @param string $file
     * @param string $destinationPath
     * @return int
     */
    protected function safeCopy(OutputInterface $output, string $file, string $destinationPath): int
    {
        if (is_writable($destinationPath)) {
            $output->writeln("<info>Copying $file to $destinationPath</info>");
            if (!copy($file, $destinationPath . '/' . basename($file))) {
                return Command::FAILURE;
            }
        } elseif (!file_exists($file)) {
            $output->writeln("<error>Error: Source file $file does not exist or is not writable</error>");
            return Command::FAILURE;
        } elseif (!is_writable($destinationPath) || !file_exists($destinationPath)) {
            $output->writeln(
                "<error>Error: Destination $destinationPath folder does not exist or is not writable</error>"
            );
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Publishing assets into public folder</info>');

        $assets = [
            'css' => [
                /**
                 * Bootstrap CSS
                 */
                'vendor/twbs/bootstrap/dist/css/bootstrap.min.css',
                'vendor/twbs/bootstrap/dist/css/bootstrap.css.map',
                'vendor/twbs/bootstrap/dist/css/bootstrap.min.css.map',

                /**
                 * Font Awesome CSS
                 */
                'vendor/components/font-awesome/css/fontawesome.min.css',
                'vendor/components/font-awesome/css/all.css',

                /**
                 * Novus D3 CSS
                 */
                'vendor/novus/nvd3/build/nv.d3.css'
            ],
            'js' => [
                'vendor/twbs/bootstrap/dist/js/bootstrap.min.js',
                'vendor/twbs/bootstrap/dist/js/bootstrap.min.js.map',
                'vendor/novus/nvd3/build/nv.d3.js',
                'vendor/novus/nvd3/build/nv.d3.js.map',
                'vendor/mbostock/d3/d3.min.js',
                'application/assets/js/default.js'
            ],
            'images' => [
                'application/assets/images/bacula-web-logo.png',
                'application/assets/images/apple-touch-icon.png',
                'application/assets/images/favicon.ico'
            ],
            'webfonts' => [
                /**
                 * Font Awesome web fonts
                 */
                'vendor/components/font-awesome/webfonts/fa-brands-400.ttf',
                'vendor/components/font-awesome/webfonts/fa-brands-400.woff2',
                'vendor/components/font-awesome/webfonts/fa-regular-400.ttf',
                'vendor/components/font-awesome/webfonts/fa-regular-400.woff2',
                'vendor/components/font-awesome/webfonts/fa-solid-900.ttf',
                'vendor/components/font-awesome/webfonts/fa-solid-900.woff2',
                'vendor/components/font-awesome/webfonts/fa-v4compatibility.ttf',
                'vendor/components/font-awesome/webfonts/fa-v4compatibility.woff2',
            ]
        ];

        // Copy css assets
        foreach ($assets['css'] as $css_file) {
            $this->safeCopy($output, $css_file, 'public/css');
        }

        // Copy javascript assets
        foreach ($assets['js'] as $js_file) {
            $this->safeCopy($output, $js_file, 'public/js');
        }

        // Copy images assets
        foreach ($assets['images'] as $img_file) {
            $this->safeCopy($output, $img_file, 'public/img');
        }

        // Copy web fonts assets
        foreach ($assets['webfonts'] as $font_file) {
            $this->safeCopy($output, $font_file, 'public/webfonts');
        }

        return Command::SUCCESS;
    }
}
