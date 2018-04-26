## Description
This is an addon for importing or exporting translatable content. It currently only supports the .xlf or .xliff file formats, but we hope to expand this to other formats as well shortly.

## Installing
1. Add the `addons/TranslationManager` folder to your `site/addons` folder.
2. Run `php please update:addons`.

## Configuration
You may setup a few optional configuration values by visiting `CP > Addons > Translation Manager`.

**Page URL (Want to add a different domain for the url of each exported page?)**  
This determines what the domain of the exported pages will be. For example, you may want to use a staging environment rather than your production site if you're using an agency for translating your content. If you leave it blank it will default to the `APP_URL` value in your .env file.

**Page Query String (Want to add a query string to the url of each exported page?)**  
If you enter a value here it will be appended to the url of each exported page. For example, your staging environment may require a token to allow access. Don't forget to include the `?` in the string.

**Exclude Pages (Want to exclude any pages? Enter page ids separated by a comma)**  
If you want to make sure some pages are ignored when exporting, enter their page IDs as a comma separated string.

**Exclude Collections (Want to exclude any collections? Enter collection slugs separated by a comma)**  
If you want to make sure some collections are ignored when exporting, enter their slugs as a comma separated string.

## Usage
Use the addon by visiting `CP > Translations`.

## License
[MIT License](http://emd.mit-license.org)