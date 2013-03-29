describe 'ocClickFocus', ->

	beforeEach module 'OC'


	beforeEach inject ($rootScope, $compile) =>
		@$rootScope = $rootScope
		@$compile = $compile
		@host = $('<div id="host"></div>')
		$('body').append(@host)
		$.fx.off = true


	it 'should focus element', =>
		elm = '<a 	href="#" ' +
					'oc-click-focus="{selector: \'#shouldfocus\'}" ' +
					'id="clicker"' +
					'onclick="this.href=\'hi\'">test</a>' +
			'<div><input id="shouldfocus" type="text" /></div>'
		@elm = angular.element(elm)
		scope = @$rootScope
		@$compile(@elm)(scope)
		scope.$digest()
		@host.append(@elm)

		$(@host).find('#clicker').trigger 'click'
		focused = document.activeElement == $(@host).find('#shouldfocus').get(0)
		expect(focused).toBe(true)



	afterEach =>
		@host.remove()