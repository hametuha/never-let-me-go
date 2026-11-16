import wpPlugin from '@wordpress/eslint-plugin';
import { createRequire } from 'node:module';
import globals from 'globals';

const require = createRequire( import.meta.url );

export default [
	{
		ignores: [ 'dist/**', 'vendor/**', 'node_modules/**', 'wordpress/**' ],
	},
	...wpPlugin.configs[ 'recommended-with-formatting' ],
	{
		languageOptions: {
			parserOptions: {
				requireConfigFile: false,
				babelOptions: {
					presets: [
						require.resolve( '@wordpress/babel-preset-default' ),
					],
				},
			},
			globals: {
				...globals.browser,
				...globals.jquery,
				NLMG: 'readonly',
			},
		},
		rules: {
			'react-hooks/rules-of-hooks': 'off',
			camelcase: 'off',
		},
	},
];
