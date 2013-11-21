GridEnhancer
============

Magento Grid Enhancer v0.3.0.

What it does
----------
The Grid Enhancer extension allows you to manage the columns in the following grids:
 - Products grid
 - Customers grid
 - Product attributes grid
 - Catalog price rules grid

Other grids may follow.  
Since in a web shop different admins need different information displayed on the grid, each admin can manage it's own grid.  
The guys in charge of marketing may want to see the special price.  
The special price may not have any value to the people in charge of delivery. They may want to see the product weight.  
Both of the above have no meaning to a the person in charge of SEO, that might want to see the meta title and keywords in the grid.  
You get the point.  

Reqiurements
--------
Magento CE 1.7.0.2 or 1.8.0.0.  
it probably works on other versions. I didn't test.  

How to use.
----------

After installation go to `Catalog->Manage products` and near the `Add product` button you should see a button called `Manage columns`.  
This button will take you to a new page where you can add columns to the grid. You cannot remove default columns.  
Click on the add column button on the top right and you should see 2 dropdowns. One with the available attributes and one with the columns that are already in the grid.  
The first one determines the attribute and the second the position.  
<img src="http://i.imgur.com/n3ZuCab.png" alt=""/>

It works the same for the other supported grids.

You can add as many columns as you want. Just don't over do it.  
When you are done click on `Save`.  
If you get tired of the columns you have you can go to the same page and delete the configuration. This will make you see the default grid.  

Warning
--------
Each admin can manage his own columns, if he has access to it.  
The configuration you set is not available for other admins.  
A way to modify what other admins see will follow (maybe).  

Limitations
--------
Now all attributes can be shown in the grid. Image attributes are not yet supported.  
Not all columns you add support sorting. This is a magento limitation. See the comment in  `Easylife_GridEnhancer_Model_Observer_Abstract::_getColumnConfig()`
Because of the way the attribute grid is constructed, you can use observers just to add columns at the beginning of the grid. I didn't want to rewrite that block also, so this is a limitation.

Rewrite and conflicts
----------
The extension rewrites the customer grid block (`Mage_Adminhtml_Block_Customer_Grid`) in order to be able to add to collection the selected columns.
The extension might conflict with other extensions that alter the grids that can be enhanced.
If the extensions that modify the product grid (and other grids) are properly written this should still work.
If you manually added columns in the grids and they do not appear in the position field modify this section of the `config.xml` by adding your columns: Xpath: `config/gridenhancer/{GRID_IDENTIFIER_HERE}/system_attributes`.

Known issue(s):
-------
If you choose to add a column after the `Name` column in the product grid and you look at the grid in a specific store then your new column will appear after the `Name` column and you cannot set it to appear after `Name in store` column.

Uninstall
-------

To uninstall the extension delete the following files and folders  
 - app/etc/modules/Easylife_GridEnhancer.xml  
 - app/locale/en\_US/Easylife\_GridEnhancer.csv  
 - app/code/community/Easylife\_GridEnhancer/  
 - app/design/adminhtml/default/default/layout/easylife\_gridenhancer.xml  
 - app/design/adminhtml/default/default/template/easylife\_gridenhancer/  
 - js/easylife\_gridenhancer/  

Run these queries on your database. Add table prefix if you have one:  
 - `DROP TABLE easylife_gridenhancer_settings;`  
 - `DELETE FROM core_resource where code = 'easylife_gridenahcer_setup'`  

Bug Report
------
<a href="https://github.com/tzyganu/GridEnhancer/issues">Please submit any bugs or feature requests here.</a>

Lessons learned
--------
IE does not like it when you add a function called "delete" inside a prototype js "class".
IE does not take into account the "selected" parameter when creating a select option using `new Option(label, value, selected)`
