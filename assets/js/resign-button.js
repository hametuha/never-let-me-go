/*!
 * Resign Button for blocks.
 *
 * @handle nlmg-resign-button
 * @deps wp-blocks, wp-block-editor, wp-i18n, wp-components
 */

const { registerBlockType } = wp.blocks;
const { InnerBlocks, useBlockProps, useInnerBlocksProps, InspectorControls } = wp.blockEditor;
const { PanelBody, TextControl } = wp.components;

const { __ } = wp.i18n;

registerBlockType( 'nlmg/resign-button', {
	apiVersion: 2,
	title: __( 'NLMG/Resign Button', 'never-let-me-go' ),
	icon: 'button',
	category: 'layout',
	description: __( 'Resign button. Only displayed to logged in users. Users will be redirected to the url of button.', 'never-let-me-go' ),
	supports: {
		className: true,
	},
	parent: [ 'nlmg/resign-block' ],
	attributes: {
		acceptance: {
			type: 'string',
			default: __( 'Accept the terms of service and leave the site.', 'never-let-me-go' ),
			source: 'html',
			selector: '.nlmg-acceptance-text',
		},
	},
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const templates = [
			[
				'core/buttons',
				{ align: 'center' },
				[
					[ 'core/button', { text: __( 'Resign', 'nlmg' ) } ],
				],
			],
		];
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Acceptance', 'kbl' ) } initialOpen={ true }>
						<TextControl label={ __( '', 'kbl' ) } value={ attributes.acceptance }
							help={ __( 'Acceptance text on check box.', 'never-let-me-go' ) }
							onChange={ ( acceptance ) => setAttributes( { acceptance } ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<span className="nlmg-resign-block-label">{ __( 'If user logged in:', 'never-let-me-go' ) }</span>
					<p className="nlmg-acceptance-paragraph">
						<label className="nlmg-accesptance-label" htmlFor="nlmg-acceptance">
							<input type="checkbox" id="nlmg-acceptance" />
							<span className="nlmg-acceptance-text">{ attributes.acceptance }</span>
						</label>
					</p>
					<InnerBlocks
						template={ templates }
						templateLock="all"
					/>
				</div>
			</>
		);
	},
	save( { attributes } ) {
		const blockProps = useBlockProps.save();
		const innerBlocksProps = useInnerBlocksProps.save();

		return (
			<div { ...blockProps }>
				<p className="nlmg-acceptance-paragraph">
					<label className="nlmg-accesptance-label" htmlFor="nlmg-acceptance">
						<input type="checkbox" id="nlmg-acceptance" />
						<span className="nlmg-acceptance-text">{ attributes.acceptance }</span>
					</label>
				</p>
				<div { ...innerBlocksProps } />
			</div>
		);
	},
} );

registerBlockType( 'nlmg/resign-login', {
	apiVersion: 2,
	title:
		__( 'NLMG/Resign Login', 'never-let-me-go' ),
	icon: 'exit',
	category: 'widgets',
	description: __( 'Notice for not logged in users.', 'never-let-me-go' ),
	supports: {
		className: true,
	},
	parent: [ 'nlmg/resign-block' ],
	edit() {
		const blockProps = useBlockProps();
		const templates = [
			[
				'core/paragraph',
				{ text: __( 'This page is only for logged-in users', 'never-let-me-go' ) },
			],
		];
		return (
			<div { ...blockProps }>
				<span className="nlmg-resign-block-label">{ __( 'Else:', 'never-let-me-go' ) }</span>
				<InnerBlocks
					template={ templates }
				/>
			</div>
		);
	},
	save() {
		const blockProps = useBlockProps.save();
		const innerBlocksProps = useInnerBlocksProps.save( blockProps );
		return (
			<div { ...innerBlocksProps } />
		);
	},
} );

registerBlockType( 'nlmg/resign-block', {
	apiVersion: 2,
	title: __( 'NLMG/Resign Block', 'never-let-me-go' ),
	description: __( 'This block displays resign button for logged-in users.', 'never-let-me-go' ),
	icon: 'groups',
	category: 'widgets',
	supports: {
		multiple: false,
	},
	attributes: {

	},
	edit() {
		const blockProps = useBlockProps();
		const templates = [
			[ 'nlmg/resign-button' ],
			[ 'nlmg/resign-login' ],
		];
		return (
			<div { ...blockProps }>
				<InnerBlocks
					template={ templates }
					templateLock="all"
				/>
			</div>
		);
	},
	save() {
		const blockProps = useBlockProps.save();
		const innerBlocksProps = useInnerBlocksProps.save( blockProps );
		return (
			<div { ...innerBlocksProps } />
		);
	},
} );
