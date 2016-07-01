## Example TS configuration for cObj XPATH ##

my.object = XPATH
my.object {

	source [URL / PATH / STRING / stdWrap]

	registerNamespace = [STRING prefix|ns]
	registerNamespace {
		getFromSource = [BOOLEAN]
		getFromSource.debug = 1
		getFromSource.listNum [TypoScript listNum]
	}

	expression [STRING + stdWrap]

	return = count|boolean|xml|array|json|string [stdWrap]

	resultObj [TypoScript split]
	resultObj {
		cObjNum = 1
		1.current = 1
	}

	implodeResult [boolean]
	implodeResult.token [string + stdWrap]

	stdWrap [stdWrap]
}