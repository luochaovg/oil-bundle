/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md.
 */

// The editor creator to use.
import BswEditorBase from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';
import EssentialsPlugin from '@ckeditor/ckeditor5-essentials/src/essentials';
import UploadAdapterPlugin from '@ckeditor/ckeditor5-adapter-ckfinder/src/uploadadapter';
import AutoFormatPlugin from '@ckeditor/ckeditor5-autoformat/src/autoformat';
import BoldPlugin from '@ckeditor/ckeditor5-basic-styles/src/bold';
import ItalicPlugin from '@ckeditor/ckeditor5-basic-styles/src/italic';
import BlockQuotePlugin from '@ckeditor/ckeditor5-block-quote/src/blockquote';
import EasyImagePlugin from '@ckeditor/ckeditor5-easy-image/src/easyimage';
import HeadingPlugin from '@ckeditor/ckeditor5-heading/src/heading';
import ImagePlugin from '@ckeditor/ckeditor5-image/src/image';
import ImageCaptionPlugin from '@ckeditor/ckeditor5-image/src/imagecaption';
import ImageStylePlugin from '@ckeditor/ckeditor5-image/src/imagestyle';
import ImageToolbarPlugin from '@ckeditor/ckeditor5-image/src/imagetoolbar';
import ImageUploadPlugin from '@ckeditor/ckeditor5-image/src/imageupload';
import LinkPlugin from '@ckeditor/ckeditor5-link/src/link';
import ListPlugin from '@ckeditor/ckeditor5-list/src/list';
import ParagraphPlugin from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import AlignmentPlugin from '@ckeditor/ckeditor5-alignment/src/alignment';
import FontSizePlugin from '@ckeditor/ckeditor5-font/src/fontsize';
import FontFamilyPlugin from '@ckeditor/ckeditor5-font/src/fontfamily';
import HighlightPlugin from '@ckeditor/ckeditor5-highlight/src/highlight';
import StrikeThroughPlugin from '@ckeditor/ckeditor5-basic-styles/src/strikethrough';
import UnderlinePlugin from '@ckeditor/ckeditor5-basic-styles/src/underline';
import GfmDataProcessorPlugin from '@ckeditor/ckeditor5-markdown-gfm/src/gfmdataprocessor';
import MediaEmbedPlugin from '@ckeditor/ckeditor5-media-embed/src/mediaembed';
import TablePlugin from '@ckeditor/ckeditor5-table/src/table';
import TableToolbarPlugin from '@ckeditor/ckeditor5-table/src/tabletoolbar';

export default class BswEditor extends BswEditorBase {
}

// Plugins to include in the build.
BswEditor.builtinPlugins = [
    EssentialsPlugin,
    UploadAdapterPlugin,
    AutoFormatPlugin,
    BoldPlugin,
    ItalicPlugin,
    BlockQuotePlugin,
    EasyImagePlugin,
    HeadingPlugin,
    ImagePlugin,
    ImageCaptionPlugin,
    ImageStylePlugin,
    ImageToolbarPlugin,
    ImageUploadPlugin,
    LinkPlugin,
    ListPlugin,
    ParagraphPlugin,
    AlignmentPlugin,
    FontSizePlugin,
    FontFamilyPlugin,
    HighlightPlugin,
    StrikeThroughPlugin,
    UnderlinePlugin,
    GfmDataProcessorPlugin,
    MediaEmbedPlugin,
    TablePlugin,
    TableToolbarPlugin
];

// Editor configuration.
BswEditor.defaultConfig = {
    toolbar: {
        items: [
            'heading',
            '|',
            'fontsize',
            'fontfamily',
            '|',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            'highlight',
            '|',
            'alignment',
            '|',
            'numberedList',
            'bulletedList',
            '|',
            'link',
            'blockquote',
            'imageUpload',
            '|',
            'mediaEmbed',
            'insertTable',
            '|',
            'undo',
            'redo'
        ]
    },

    image: {
        styles: [
            'full',
            'alignLeft',
            'alignRight'
        ],
        toolbar: [
            'imageStyle:alignLeft',
            'imageStyle:full',
            'imageStyle:alignRight',
            '|',
            'imageTextAlternative'
        ]
    },

    table: {
        contentToolbar: [
            'tableColumn',
            'tableRow',
            'mergeTableCells'
        ]
    },

    // This value must be kept in sync with the language defined in webpack.config.js.
    language: 'zh-cn'
};
