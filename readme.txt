=== Wp-Pro-Quiz ===
Contributors: xeno010
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KCZPNURT6RYXY
Tags: quiz, test, answer, question, learning, assessment
Requires at least: 3.3
Tested up to: 4.3
Stable tag: 0.37
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A powerful and beautiful quiz plugin for WordPress.

== Description ==

A powerful and beautiful quiz plugin for WordPress.

= Functions = 
* Single Choice
* Multiple Choice
* "Sorting" Choice
* "Free" Choice
* "Matrix Sorting" Choice
* Cloze function
* Assessment
* Timelimit
* Random Answer
* Random Question
* HTML in questions and answers is allowed
* Multimedia in questions
* Back-Button
* Correct / incorrect response message for all questions
* Different valency for every question
* Different points for each answer
* Result text with gradations
* Preview-function
* Statistics
* Leaderboard
* Quiz requirements
* Hints
* Custom fields
* Import / Export function
* E-mail notification
* Category support
* Quiz-summary
* Many configuration options
* Really nice standard design
* Mighty
* Fully compatible with cache plugins (e.g. WP-Super-Cache or W3 Total Cache)


= Translations =
* Arabic / عربي (Thanks Abuhassan)
* Brazilian Portuguese / Português do Brasil (Thanks Gabriel V.)
* Chinese (Traditional) (Thanks Dinno Lin)
* Czech / čeština (Thanks Petr Š.)
* Danish / dansk (Thanks Kenneth D.)
* Dutch / nederlands (Thanks Bas W. and Jurriën van den H.)
* English (Thanks Alexander M.)
* Finnish / Suomi (Thanks Mikko Sävilahti)
* French / français (Thanks Aurélien C.)
* German / deutsch
* Greek / ελληνικά (Thanks Ζαχαρίας Σ.)
* Hungarian / magyar (Thanks Webstar Csoport Kft.)
* Indonesian / Bahasa Indonesia (Thanks dieka91 and Creative Computer Club)
* Italian / Italiano (Thanks Pacaldi and Fabio)
* Korean / 한국어 (Thanks Kyeong Choi)
* Norwegian / norsk (Thanks Stein Ivar J.)
* Persian / فارسی (Thanks Behrooz N.)
* Polish / polski (Thanks Piotr Sz. BaGGietka)
* Russian / русский (Thanks Sergei B. and Alex A.)
* Slovak / slovenščina (Thanks Martin D.)
* Spanish / español (Thanks Carlos R.)
* Swedish / svenska (Thanks Martin J.)
* Turkish / Türkçe (Thanks Nsaral)

= Live Demo =
http://www.it-gecko.de/wp-pro-quiz-quiz-plugin-fuer-wordpress.html (scroll to "Demo")

= Special =
* Support for "User Role Editor" etc.
* Support for BuddyPress achievements 3.x.x

= Wp-Pro-Quiz is now on Github! =
https://github.com/xeno010/Wp-Pro-Quiz

= Wp-ProQuiz - Wiki =
https://github.com/xeno010/Wp-Pro-Quiz/wiki

= Support = 
* English: http://wordpress.org/support/plugin/wp-pro-quiz
* German/Deutsch: http://www.it-gecko.de/kontakt

== Installation ==

1. Upload the wp-pro-quiz folder to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Quiz demo - Start page
2. Quiz demo - Correkt message
3. Quiz demo - Multimedia
4. Quiz demo - Results
5. Quiz demo2 - Time limit and back button
6. Quiz demo - Leadboard
7. Quiz demo - Average score
8. Adminmenu - Quiz overview
9. Adminmenu - Create quiz
10. Adminmenu - Quiz question overview
11. Adminmenu - Create question
12. Adminmenu - Question statistics

== Changelog ==

= 0.37 =
* New frontend init structure
* Added gulp support
* Fix #7 - compatibility with All In One WP Security (https://github.com/xeno010/Wp-Pro-Quiz/issues/7)
* Fix #5 - order of questions changes (https://github.com/xeno010/Wp-Pro-Quiz/issues/5)
* Merge pull request #6 - escape double quotes that breaks <input> tag (https://github.com/xeno010/Wp-Pro-Quiz/pull/6)
* Translation for polish have been added (Thanks Behrooz N.)

= 0.36 =
* Fix #3 (Missing translation) (https://github.com/xeno010/Wp-Pro-Quiz/issues/3)
* Fix #2 - Free Answer: "0" Answer is marked as incorrect (https://github.com/xeno010/Wp-Pro-Quiz/issues/2)
* Translation for korean have been added (Thanks Kyeong Choi)
* Translation for finnish have been added (Thanks Mikko Sävilahti)
* Updated italian translation (Thanks Fabio)

= 0.35 =
* Bugfix
* New quiz and question overview design
* New menu structure

= 0.34 =
* Bugfix

= 0.33 =
* Translation for hungarian have been added (Thanks Webstar Csoport Kft.)
* Added "solved" display in statistic-overview
* Added option: show custom forms in statistics table

= 0.32 =
* CSS Update for Themes "Respo", "editor" and "twentyfourteen"
* fixed white space problem in front quiz view
* fixed bug in Import handler
* Fixed bug (Illegal string offset '\')
* Translation for italian have been added (Thanks Pacaldi)
* Translation for slovak have been added (Thanks Martin D.)

= 0.31 =
* small bugfix

= 0.30 =
* small bugfix

= 0.29 =
* Quiz categories added
* Support for custom fields (variables) in Email and "quiz result"
* Translation for chinese (traditional)  have been added (Thanks Dinno Lin)
* Translation for turkish have been added (Thanks Nsaral)
* Updated spanish translation (Thanks Carlos Ruiz)
* Updated greek translation (Thanks Ζαχαρίας Σδρέγας)
* Updated russian translation (Thanks Сергей Бондаренко)
* Updated dutch translation (Thanks Anton Timmermans)

= 0.28 =
* Bugfix in custom field at the option "Display position - At the end of the quiz"
* Bugfix in Statistc-function
* Translation for indonesian have been added (Thanks dieka91 and Creative Computer Club)

= 0.27 =
* Statistics function has been completely overworked.
 - All answers from users are stored now.
 - Edit, delete or add questions has no effect on existing results.
* Statistics function can also be actived with the option "Show only specific number of questions" now.
* The "Show only specific number of questions" option now works with cache plugins.
* added custom fields
* Quiz mode "Questions below eachother" can now be divided into pages.
* Quiz: added option "Sort questions by category" - Sort questions by category.
* Quiz: added option "Display category" - Category is displayed in the questions.
* Repair Database - Added button in the global settings
* Improved matrix-sorting question type by allowing sort elements to be dragged into any criterion having the same text as the correct answer. (For example, if there are 6 unique sort elements and only 2 unique criterion, then dragging a sort element into any criterion with the same correct name will validate as correct.)
 - Thanks Grant K Norwood (grantnorwood)
* Added ability to set the table column width for matrix sorting criteria in order to allow longer criteria text. The option is displayed only when matrix sorting is selected as the answer type.
 - Thanks Grant K Norwood (grantnorwood)

= 0.26 =
* Bugfix: Cloze choice and assessment
* Bugfix: Email sending

= 0.25 =
* Categories overview in the email
* Autostart option added
* Support for XML import and exports added
* Force user to answer a question
* New point calculation for single choice (new option "different points - mode 2" added)
* Option "hide question position overview" added
* Option "hide question numbering" added
* Updated greek translation
* Updated dutch translation
* Translation for czech have been added (Thanks Petr Š.)

= 0.24 =
* Support for Achievements V3 added
* Support for Achievements V2 removed
* Improvement of statistics function
* TinyMCE editor added to E-Mail settings
* Assessment choice added
* Time logger for each question added
* Option "Show category score" added in Quiz-result-site
* User e-mail support added (send an email with quiz-result to the user)
* "Question overview" in "View questions" will now be displayed
* Rename button "next exercise" to "next"
* Rename last button "next exercise" to "Finish quiz" or "Quiz-summary"
* Bugfix for IIS
* Adminmenu: "Media add" button in "answers" (edit/new question) added
* Adminmenu: show question-category in question overview
* Adminmenu: option "Hide correct questions - display" added
* Adminmenu: option "Hide quiz time - display" added
* Adminmenu: option "Hide score - display" added
* Updated russian translation
* Updated dutch translation
* Updated greek translation
* Translation for danish have been added (Thanks Kenneth D.)
* Translation for french have been added (Thanks Aurélien C.)

= 0.23 =
* Automatically add to the leaderboard
* Leaderboard is updated automatically
* Cloze-Choice: several words per gap are possible
* Quiz Summary added
* Skip question  button added
* Email notification added
* Category support added
* Review Question added
* CSS-adjustments
* Bug fixes
* Translation for spanish have been added (Thanks Carlos R.)
* Translation for greek have been added (Thanks Ζαχαρίας Σ.)

= 0.22 =
* CSS Bugfixes
* IIS bug fixed
* Time limit improves (JS)
* Updated norwegian translation
* Updated russian translation
* Updated dutch translation

= 0.21 =
* Hard fail in version 0.21 (all question points reset)

= 0.20 =
* Bugfix: in "Cloze": not correctly points calculated
* Bugfix: "Number answers" option broken. all answers are numbered
* Bugfix: Database

= 0.19 =
* Leaderboard added
* Quiz requirements added
* Different points for each answer
* "Matrix Sort" sort elements can now be created without criteria
* Front-End javascript completely rewritten
* Admin javascript revised
* Average score can now be displayed in quiz
* Cloze: different points can be assigned for every gap
* Very many internal changes
* several bugfixes

= 0.18 =
* "Allow HTML" bug fixed
* "0" can now be used as a answer
* Database: "sort" change to SMALLINT
* Updated Norwegian translation
* Updated Russian translation

= 0.17 =
* 0.17 is 0.16 (WordPress SVN bug)

= 0.16 =
* Bug in uninstall script fixed
* Option "hide correct- and incorrect-message" added
* Option "correct- and incorrect-answermark" added
* Option "show only specific number of questions" added
* Bugfix in statistic function
* Translation for Dutch have been added (Thanks Bas W.)
* Translation for Norwegian have been added (Thanks Stein Ivar J.)

= 0.15 =
* Typo corrected
* Adjustment of admin template
* Internal changes
* Capability added
* Support for BuddyPress achievements added
* Support for "User Role Editor" etc. added
* Statistic function expanded
* Points now can be entered per correct answer instead of correct question
* Translation for Russian have been added (Thanks Sergei B.)
* Translation for Swedish have been added (Thanks Martin J.)

= 0.14 =
* Bugfix in the statistics function
* "Questions below each other" option was added
* "Number answers" option was added

= 0.13 =
* Bugfix
* New screenshots
* A new Touch Library was added for mobile devices
* Statistics function has been extended
* Setting page in case of problems added
* "Copy questions from another Quiz" function added
* "Execute quiz only once" option added

= 0.12 =
* Compatible for WordPress 3.5
* Translation for Arabic have been added (Thanks Abuhassan)
* added hide "restart quiz" button option
* added hide "view question" button option
* Bugfix in sorting choice with IE & Safari

= 0.11 =
* Bugfix in javascript-code
* "Sort elements" are always randomly arranged
* Bugfix in CSS for different themes

= 0.10 =
* Bugfix: "Matrix Sorting" in connection with "Random answer"
* Bugfix: Database in connection with UTF-8
* Bugfix in cloze
* Bugfix in the backend

= 0.9 =
* Bugfix in the frontend (Single choice)

= 0.8 =
* Bugfix in the frontend and backend

= 0.7 =
* CSS: !important added to all CSS-properties

= 0.6 =
* For every question you can now individually be determined
* Cloze answer type added
* Import / export function added

= 0.5 =
* New choice: "Matrix Sorting" choice
* Result text now can be graduated
* CSS Bugfix

= 0.4 =
* added hint support
* bug in sort choice were fixed
* mistranslations were fixed

= 0.3 =
* added version number for js and css

= 0.2 =
* bugfix
* add statistics function
* small changes

= 0.1 =
* release
