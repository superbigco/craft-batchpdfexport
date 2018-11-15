# Batch PDF Export plugin for Craft CMS 3.x

Mass export PDF invoices in one go.

![Screenshot](resources/icon.png)

## Requirements

This plugin requires Craft CMS 3.0.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require superbig/craft-batchpdfexport

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Batch PDF Export.

## Batch PDF Export Overview

After you install it, the option Export PDFs will be available under the element actions menu when you select 1 or more order in the Commerce orders list.

## Configuring Batch PDF Export

This plugin uses the settings _Order PDF Template_ and _Order PDF Filename Format_ in Commerce -> General Settings to generate the PDFs.

In general, if the normal PDF download works, the batch export should also work.

## Using Batch PDF Export

1. Select one or more order in the Orders list and click the action dropdown in the toolbar
2. Select Export PDFs 

Brought to you by [Superbig](https://superbig.co)
