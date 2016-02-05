# form definitions

How to create forms
-------
- One file per form, which implements a FormHelper via the DataForm class.
- Form files definitions contain blocks named after UI elements

elementType { key {value} ... }

- valid element types: text, multiline, slider, select, radio, submit
- All elements have a key called 'html' which can have multiple values that translate to the tag.
- Example:  text { html { class "myclass" } }
- All elements have a key called 'data' which sets the value based on data passed to DataForm()
- To load, in php, $df=new DataForm( 'formfile.txt' );
- To assign data to the form, the form must have elements with matching 'data' clauses, and this data must be passed to the DataForm constructor, ie: $df=new DataForm( 'somefile.txt', $prepopulateData ); where in somefile.txt there is something like text { data 'mydbfield' } which associates that text input with that data field.
