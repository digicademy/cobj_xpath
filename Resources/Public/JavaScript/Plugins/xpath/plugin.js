CKEDITOR.plugins.add( 'xpath', {
    icons: 'xpath',
    init: function( editor ) {

        editor.addCommand( 'xpath', new CKEDITOR.dialogCommand( 'xpathDialog', {
            allowedContent: 'xpath[expression,return]',
            requiredContent: 'xpath[expression,return]'
        } ) );

        editor.ui.addButton( 'Xpath', {
            label: "XPATH",
            command: 'xpath',
            toolbar: 'insert'
        });

        if ( editor.contextMenu ) {
            editor.addMenuGroup( 'xpathGroup' );
            editor.addMenuItem( 'xpathItem', {
                label: 'Edit XPATH',
                icon: this.path + 'icons/xpath.png',
                command: 'xpath',
                group: 'xpathGroup'
            });

            editor.contextMenu.addListener( function( element ) {
                if ( element.getAscendant( 'xpath', true ) ) {
                    return { abbrItem: CKEDITOR.TRISTATE_OFF };
                }
            });
        }

        CKEDITOR.dialog.add( 'xpathDialog', this.path + 'dialogs/xpath.js' );
    }
});

/*
Currently it is not possibly to display a text label besides a custom button in TYPO3's CKEDITOR implementation.
For showing texts besides a button (like with the "source" button the CSS styles in the skin would have to be
overriden by a custom CSS stylesheet in the head of CKEDITORS iframe in the backen. But there is no way to
add a custom stylesheet to this iframe. Workaround: Since the plugin.js of this extension is executed in the
head of the relevant iframe, the following JS functions directly inject the needed CSS rule.
*/

var addRule;

if (typeof document.styleSheets != "undefined" && document.styleSheets) {
    addRule = function(selector, rule) {
        var styleSheets = document.styleSheets, styleSheet;
        if (styleSheets && styleSheets.length) {
            styleSheet = styleSheets[styleSheets.length - 1];
            if (styleSheet.addRule) {
                styleSheet.addRule(selector, rule)
            } else if (typeof styleSheet.cssText == "string") {
                styleSheet.cssText = selector + " {" + rule + "}";
            } else if (styleSheet.insertRule && styleSheet.cssRules) {
                styleSheet.insertRule(selector + " {" + rule + "}", styleSheet.cssRules.length);
            }
        }
    }
} else {
    addRule = function(selector, rule, el, doc) {
        el.appendChild(doc.createTextNode(selector + " {" + rule + "}"));
    };
}

function createXpathCssRule(selector, rule, doc) {
    doc = doc || document;
    var head = doc.getElementsByTagName("head")[0];
    if (head && addRule) {
        var styleEl = doc.createElement("style");
        styleEl.type = "text/css";
        styleEl.media = "screen";
        head.appendChild(styleEl);
        addRule(selector, rule, styleEl, doc);
        styleEl = null;
    }
};

createXpathCssRule(".cke_button__xpath_label", "display: inline !important;");
