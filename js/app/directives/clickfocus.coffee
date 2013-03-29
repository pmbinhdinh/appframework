###

ownCloud - App Framework

@author Bernhard Posselt
@copyright 2012 Bernhard Posselt nukeawhale@gmail.com

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
License as published by the Free Software Foundation; either
version 3 of the License, or any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU AFFERO GENERAL PUBLIC LICENSE for more details.

You should have received a copy of the GNU Affero General Public
License along with this library.  If not, see <http://www.gnu.org/licenses/>.

###

# Used to focus an element when you click on the element that this directive is
# bound to
# The selector attribute needs to defined, this element will be focused
# lost

angular.module('OC').directive 'ocClickFocus', ->

	return (scope, elm, attr) ->
		options = scope.$eval(attr.ocClickFocus)

		if angular.isDefined(options) and angular.isDefined(options.selector)
			elm.click ->
				$(options.selector).focus()

