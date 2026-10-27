import wpPlugin from '@wordpress/eslint-plugin';
import globals from 'globals';

export default [
	...wpPlugin.configs[ 'recommended-with-formatting' ],
	{
		languageOptions: {
			globals: {
				...globals.browser,
				...globals.jquery,
				NLMG: 'readonly',
			},
		},
		rules: {
			'react-hooks/rules-of-hooks': 0,
			camelcase: 0,
		},
	},
];
