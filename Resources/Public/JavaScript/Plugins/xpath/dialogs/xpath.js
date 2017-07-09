CKEDITOR.dialog.add( 'xpathDialog', function( editor ) {
    return {
        title: 'XPATH properties',
        minWidth: 400,
        minHeight: 200,
        contents: [
            {
                id: 'tab-basic',
                label: 'Settings',
                elements: [
                    {
                        type: 'text',
                        id: 'xpath',
                        label: 'Path to XML source',
                        validate: CKEDITOR.dialog.validate.notEmpty( "XML source field cannot be empty." ),

                        setup: function( element ) {
                            this.setValue( element.getText() );
                        },

                        commit: function( element ) {
                            element.setText( this.getValue() );
                        }
                    },
                    {
                        type: 'text',
                        id: 'expression',
                        label: 'XPATH expression',
                        validate: CKEDITOR.dialog.validate.notEmpty( "XPATH expression field cannot be empty." ),

                        setup: function( element ) {
                            this.setValue( element.getAttribute( "expression" ) );
                        },

                        commit: function( element ) {
                            element.setAttribute( "title", this.getValue() );
                        }
                    },
                    {
                        type: 'select',
                        id: 'return',
                        label: 'Return format',
                        items: [ [ 'array' ], [ 'boolean' ], [ 'count' ], [ 'json' ], [ 'string' ], [ 'xml' ] ], 'default' : 'string',
//                        validate: CKEDITOR.dialog.validate.notEmpty( "Return format field cannot be empty." ),

                        setup: function( element ) {
                            this.setValue( element.getAttribute( "return" ) );
                        },

                        commit: function( element ) {
                            element.setAttribute( "title", this.getValue() );
                        }
                    }
                ]
            }
        ],

        onShow: function() {
            var selection = editor.getSelection();
            var element = selection.getStartElement();

            if ( element )
                element = element.getAscendant( 'xpath', true );

            if ( !element || element.getName() != 'xpath' ) {
                element = editor.document.createElement( 'xpath' );
                this.insertMode = true;
            }
            else
                this.insertMode = false;

            this.element = element;
            if ( !this.insertMode )
                this.setupContent( this.element );
        },

        onOk: function() {
            var dialog = this;

            var xpath = editor.document.createElement( 'xpath' );
            xpath.setAttribute( 'expression', dialog.getValueOf( 'tab-basic', 'expression' ) );
            xpath.setAttribute( 'return', dialog.getValueOf( 'tab-basic', 'return' ) );
            xpath.setText( dialog.getValueOf( 'tab-basic', 'xpath' ) );

            editor.insertElement( xpath );
        }

    };
});
