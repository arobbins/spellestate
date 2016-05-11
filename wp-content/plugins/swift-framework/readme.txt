=== Swift Framework ===
Contributors: SwiftIdeas
Tags: swift framework
Requires at least: 3.6
Tested up to: 4.5
Stable tag: 4.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Swift Framework plugin, for Swift Ideas themes.

== Description ==

The Swift Framework plugin provides the Custom Post Types, Swift Slider, and Swift Page Builder. Custom Post Types are available for any theme, but Swift Slider & Swift Page Builder require a Swift Ideas Theme (running the latest framework) to function correctly.

Supported Themes: Atelier, Cardinal, JOYN

== Changelog ==

= 2.1.7 =
- FRONTEND: Fixed FAQs category issue.
- BACKEND: Fixed issue with return key not working in text view.

= 2.1.6 =
- BACKEND: Fixed unexpected error with v2.1.5

= 2.1.5 =
- FRONTEND: Improved image loading by adding srcset functionality in various places such as image element, image banner element, and gallery sliders
- FRONTEND: Fixed issue with tour display
- FRONTEND: Fixed ajax add to cart issue with add to cart button shortcode
- BACKEND: Fixed issue with text block having extra margin by default if override margin has been set
- BACKEND: Fixed legacy design fields not showing as inactive
- BACKEND: Fixed Google maps buttons fields
- BACKEND: Fixed extra class field being overridden in child elements by the row
- BACKEND: Removed duplicate extra class field on row
- BACKEND: Fixed asset name enter keypress event
- BACKEND: Text in text blocks is now forced dark colour, to avoid being unable to see the text when being set to a light colour
- BACKEND: Multi-layer Parallax display fix
- BACKEND: Various design option fixes

= 2.1.1 =
- FRONT-END: Fixed issue with Portfolio Showcase error.
- BACKEND: Re-added Redux Framework to the plugin.

= 2.1.0 =
- FRONT-END: Added overflow left/right option for image asset for use in full-width rows
- FRONT-END: You can now select a display layout in the Products page builder asset
- BACKEND: Added design tab for margin/padding/border controls on columns, rows, and text blocks
- BACKEND: Improved responsive + touch capabilities for editing pages on touch/mobile devices.
- BACKEND: Added option to set the default width of the element edit modal
- BACKEND: Performance improvements
- BACKEND: Fixed issue with edit modal header disappearing.
- BACKEND: Fixed issue with controls on 1/6 elements + columns
- BACKEND: Edit modal header is now stuck to the top of the edit modal, allowing for save/cancel without scrolling
- BACKEND: Improved element searching, showing no results if none are found rather than all.
- BACKEND: Fixed compatibility issue with NinjaForms
- BACKEND: Removed redux framework as provided with the theme or plugin instead.
- BACKEND: Boxed Content now shows inner content if the Text Block show content option is enabled in the page builder options

= 2.0.3 =
- FRONT-END: reverted button changes to use previous parameter names.
- BACKEND: Removed empty page builder animation
- BACKEND: Added most used functionality, allowing you to quickly access your most used elements
- BACKEND: Alert Asset fixes
- BACKEND: Fixed Scroll bar in the Edit modal that couldn’t be selected
- BACKEND: The sizes (height) of the resize handle was changed to avoid resize conflicts in elements inside each others (like in tabs, accordions, tours, Row).
- BACKEND: Fixed issues with dragging elements to the bottom of the page builder area. Was fixed in Row, Tour, Tabs, Accordions
- BACKEND: Fixed issues with elements added to the tabs, Accordions, tours

= 2.0.2 =
- FRONTEND: further icon box fixes
- FRONTEND: Added compatibility for Portfolio WPML duplication
- BACKEND: Added option to show text block text within the element preview, like in older versions
- BACKEND: Fixed issue with Toggle display
- BACKEND: Fixed issue with responsive visibility dropdown being cut off
- BACKEND: Fixed issue with Widget Area element
- BACKEND: Fixed issue with products element

= 2.0.1 =
* FRONTEND: Fixed issue with button output
* FRONTEND: Fixed issue with icon box image display
* FRONTEND: Fixed issue with icon box width
* BACKEND: Fixed issue with product category select
* BACKEND: Fixed PHP7 issue which prevented edit fields from loading data
* BACKEND: Fixed resize issue
* BACKEND: Fixed image banner element styling to match image element
* BACKEND: Fixed issue with dragging and dropping resized elements

= 2.0.0 =
* Completely overhauled the Swift Page Builder, now much slicker, quicker and easier to use!
* If you have any issues with the new builder, please log a support topic related to your specific theme at http://swiftideas.com/support

= 1.7.7 =
* SPB Search now can set post type to Any or Products
* Minor fixes

= 1.7.6 =
* Fixed missing content overview on page builder assets

= 1.7.5 =
* Added support for PHP7
* Added Animated Headline page builder asset
* Added ordering options for Directory page builder asset
* Updated Redux framework
* Fixed compatibility issue with Visual Composer
* Fixed WPML duplication issue with Swift Slider

= 1.7.0 =
* Minor fixes

= 1.66 =
* Migrated to new plugin update hosting.
* Added post type override option to recent posts page builder asset.
* Fixed issue with product category setting in products page builder asset.
* Fixed issue where lightbox icon would show on galleries where no lightbox image was present.

= 1.65 =
* Temporary fix for image caption, reverted to a textfield input until html input is resolved 

= 1.64 =
* Fixed progress bar styling
* Fixed stroke-to-fill button styling
* Fixed issue with page builder options not showing when certain requirements were met
* Fixed bootstrap conflict
* Fixed issue with image caption formatting
* Removed unecessary admin script from loading

= 1.63 =
* Updated Redux framework

= 1.62 =
* Team gallery display images are now clickable on mobile
* Updated minified files after previous update
* Updated Redux framework

= 1.61 =
* Slide video now only starts playing when the slider is in the viewport
* Image caption field now supports HTML (line breaks etc)
* Updated Redux framework

= 1.60 =
* Added loop on/off option to Row background video
* Videos set not to loop now won't restart when scrolled into view
* Fixed row/column layout issues
* Fixed issue with social output
* Fixed issue with page builder not loading when on the s"Text" content tab
* Fixed background position align of Swift Slider

= 1.51 =
* Disabled options developer mode
* Multi-layer parallax display fixes
* Updated language files


= 1.50 =
* Added options panel for all framework related options
* Added background image horizontal alignment and size options to Swift Slider slides
* Fixed further issues with intro animations
* Fixed issue with campaign category display

= 1.43 =
* Fixed issues with certain animation issues

= 1.42 =
* Properly fixed missing css images

= 1.41 =
* Swift Slider videos now don't reset when scrolled off the page
* Fixed missing css images

= 1.40 =
* Consolidated Page Builder asset css
* Added option to add full slide link to Swift Slider slides
* Fixed issue with translation files being named incorrectly
* Fixed page builder controls not being accessible on touch devices
* Fixed issue with map asset display type select
* Minor improvements to SPB drag & drop

= 1.32 =
* Removed selected product select options to greatly increase performance, now comma delimited text input
* Fixed faqs content output

= 1.31 =
* Refactored selected products query to massively improve performance
* Adjusted page builder asset loading script to ignore non-php files
* Adjusted swift slider caption size on mobile

= 1.30 =
* Added "Title" field to Go Pricing asset
* Added Carousel & Column Count options to "Standard Row" Recent Posts display type
* Added 1 column option to Products asset
* Updated noUISlider plugin script
* Fixed issue with column padding
* Fixed issue with swift slider parallax causing items to not be clickable in the main content

= 1.21 =
* Minor bug fixes

= 1.20 =
* Swift Slider pagination now shows permanently on mobile
* Improved controls display on 1/6 column elements
* Fixed issue where captions could not be disabled on gallery asset masonry display
* Fixed issue with Swift Slider on boxed layout modes
* Fixed issue with map controls not disabling/enabling
* Fixed issue with Swift Slider parallax on mobile

= 1.10 =
* Greatly improved Page Builder save page speed
* Improved theme compatibility 
* Fixed issue with Swift Slider background vertical alignment

= 1.03 =
* Fixed comaptibility issues with Visual Composer
* Fixed issue with Swift Slider curtain not allowing scroll down

= 1.02 =
* Added fixes for potential missing function errors
* Portfolio post type now supports revisions

= 1.01 =
* Added language files
* Fixed issue where image option wouldn’t show when using Video for a Row
* Added option to set a custom image height in the Team asset

= 1.0 =
* Inital version for release with Atelier