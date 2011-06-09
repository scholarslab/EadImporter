

  About EAD Importer 

----------

The EAD Importer is developed by the [Scholars' Lab][1] at the University of Virginia Library.  The user may select an EAD finding aid from their hard drive to upload to the server, where the XML undergoes an XSL transformation.  Each item is extracted from the EAD document as a row in a CSV file.  EAD metadata is mapped to Dublin Core.  The CSV file is placed in the CSV Import plugin (required) csv_files folder.  The file is then imported into the Omeka database using the CSV Import plugin.  EadImporter allows for simple string filtering.

  Download 

----------

Version:

* [EadImporter 1.0][2]

  Features 

----------

The plugin also has the following features:

* Filter items that contain a matching word or phrase.
* Select collection or public/featured on import.
* Imports multiple EAD files at the same time.
* Undos--or deletes-- imports (via CSV Import). 
* Allows users to view and clear import history (via CSV Import).

  Needed Features 

----------

* More advanced filtering, including date ranges.

  Preparing for EAD Importer 

----------

1.  The EAD document must be well formed.  The @level attribute on items is recommended, but not required.  If there is no @level, components that do not have children will be assumed to be items by the XSL stylesheet.

  Installing and Configuring 

----------

1.  Download and install the CSV Importer(see [CsvImport][3]).  Refer to the installation instructions for that plugin first.

2.  Be sure that the packages php5-cli and php5-xsl are installed.  XSLTProcessor is required for the plugin to function and CLI is required for the CSV Importer.  These can be installed through package managers on most Linux systems.  Refer to Google for instructions for installing the packages on your operating system.

3.  Set the correct write permissions to xml_dump in the EadImporter plugin folder.  The system will place uploaded XML files here.

4.  Download and install the EAD Importer(see [Installing_a_Plugin][4]).

5.  Click the EAD Import administrative tab.

6.  Select file to upload from the hard drive. Select other import or filter options, if necessary. Upload the document.

7.  Import status can be seen by clicking on the CSV Import administrative tab and selecting the Status tab under the CSV Import section.

  Undo an Import 

----------

1.  Click on CSV Import administrative tab.

2.  Click Status tab.

3.  Click Undo link for the Import you want to undo. This will delete all items for this import.

<!-- 
NewPP limit report
Preprocessor node count: 8/1000000
Post-expand include size: 0/2097152 bytes
Template argument size: 0/2097152 bytes
Expensive parser function count: 0/100
-->

Retrieved from "[http://omeka.org/codex/Plugins/EadImporter](http://omeka.org/codex/Plugins/EadImporter)"

[1]: http://scholarslab.org/ "http://scholarslab.org/"
[2]: http://www.scholarslab.org/wp-content/uploads/2010/07/EadImporter-1.0.zip "http://www.scholarslab.org/wp-content/uploads/2010/07/EadImporter-1.0.zip"
[3]: http://omeka.org/codex/Plugins/CsvImport "http://omeka.org/codex/Plugins/CsvImport"
[4]: http://omeka.org/codex/Installing_a_Plugin "Installing a Plugin"
