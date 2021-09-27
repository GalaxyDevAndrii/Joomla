(function($) {
    var $previewiframe, previewiframe, cssTplStyle, cssTplPalette, t4_site_root_url, t4previewkey, tempId;

    var addPreviewInfo = function(url) {
        if (url.indexOf('templateStyle') == -1) {
            url += url.indexOf('?') > -1 ? '&' : '?';
            url += 't4preview=' + t4previewkey + '&templateStyle=' + tempId;
        }
        var layoutpreview = $('#t4-preview-layout').prop('checked');
        if (layoutpreview) {
            url += '&t4previewlayout=1';
        }

        return url;
    }
    var removeParamUrl = function(url, name) {
        var linkbase = url.match(/^[^\#\?]+/) ? url.match(/^[^\#\?]+/)[0] : t4_site_root_url;
        var parammer = url.match(/\?(.+)+$/) ? url.match(/\?(.+)+$/)[1].split('&') : [];
        parammer.forEach(function(val, i) {
            if (val.indexOf(name) != -1) {
                parammer.splice(i, 1);
            }
        });
        var changeUrl = (parammer.length !== 0) ? linkbase + "?" + parammer.join("&") : linkbase;
        return changeUrl;
    }
    var removePreviewInfo = function(url) {
        url = removeParamUrl(url, 't4preview');
        url = removeParamUrl(url, 'templateStyle');
        url = removeParamUrl(url, 't4previewlayout');
        // remove t4prevew & templateStyle params
        // store
        return url;
    }

    var getPreviewLink = function(link) {
        if (!link) link = localStorage.getItem('last_preview_link_' + tempId);
        if (!link) {
            // get default link
            link = t4_site_root_url + $previewiframe.data('link');
        }

        if (!link || link.indexOf(t4_site_root_url) !== 0) link = t4_site_root_url;

        return addPreviewInfo(link);
    }

    var updatePreviewLink = function (link) {
        if (!link || link.indexOf(t4_site_root_url) !== 0) return;
        localStorage.setItem('last_preview_link_' + tempId, removePreviewInfo(link));
    }

    var removePreviewLink = function () {
        localStorage.removeItem('last_preview_link_' + tempId)
    }

    var previewTimeout = 0;
    var reloadPreview = function(link) {
        clearTimeout(previewTimeout);
        previewTimeout = setTimeout(function(){_reloadPreview(link)}, 100);
    }

    var _reloadPreview = function (link) {
        if(typeof link !== 'string') link = '';
        // no preview for external link
        if (link && link.indexOf(t4_site_root_url) !== 0) {
            link = '';
        }
        var previewlink = getPreviewLink(link);
        if (previewiframe.src != previewlink) {
            previewiframe.src = previewlink;
            // store for next view
            updatePreviewLink(previewlink);
        } else {
            previewiframe.contentWindow.location.reload();
        }
    }

    var reloadAssets = function(e) {

        // load fonts
        // apply css
        // compile style.tpl.css
        var params = {};
        var root_Lighten_Darken = [];
        var root_css = [];
        root_Lighten_Darken.push(":root{");
        root_css.push(":root{");
        var base_color = ['color_blue','color_indigo','color_purple','color_pink','color_red','color_orange','color_yellow','color_green','color_teal','color_cyan'];
        // get all styles value
        $('[name^="typelist-theme["]').each(function() {
            var $input = $(this),
                name = this.name.substring('typelist-theme['.length, this.name.length - 1),
                val = $input.val();
            if (name.match(/_color$/) && !val.match(/^(#|rgb)/)) {
                val = $input.data('val') ? $input.data('val') : val;
                val = val.replace(/\s/g, '-').replace(/_/g, '-').toLowerCase();

                if(val.match(/user/)){
                    val = val.replace(/user-/g, '').toLowerCase();
                }
                params[name] = 'var(--' + val + ')';
            }
            if(base_color.indexOf(name) > -1){
                root_Lighten_Darken.push('--'+name.replace(/_/g,"-")+"-100" +":"+ tinycolor(val).lighten(40).toHexString()+";");
                root_Lighten_Darken.push('--'+name.replace(/_/g,"-")+"-200" +":"+ tinycolor(val).lighten(30).toHexString()+";");
                root_Lighten_Darken.push('--'+name.replace(/_/g,"-")+"-300" +":"+ tinycolor(val).lighten(20).toHexString()+";");
                root_Lighten_Darken.push('--'+name.replace(/_/g,"-")+"-400" +":"+ tinycolor(val).lighten(10).toHexString()+";");
                root_Lighten_Darken.push('--'+name.replace(/_/g,"-")+"-500" +":"+ tinycolor(val).lighten(0).toHexString() +";");
                root_Lighten_Darken.push('--'+name.replace(/_/g,"-")+"-600" +":"+ tinycolor(val).darken(10).toHexString()+";");
                root_Lighten_Darken.push('--'+name.replace(/_/g,"-")+"-700" +":"+ tinycolor(val).darken(20).toHexString()+";");
                root_Lighten_Darken.push('--'+name.replace(/_/g,"-")+"-800" +":"+ tinycolor(val).darken(30).toHexString()+";");
                root_Lighten_Darken.push('--'+name.replace(/_/g,"-")+"-900" +":"+ tinycolor(val).darken(40).toHexString()+";");
            }
            if(name != 'styles_palettes'){
                params[name] = val;
                root_css.push('--'+name.replace(/_/g,"-") +":"+ val+";");
            }
        });
        $('[name^="typelist-site["]').each(function(){
            var $input = $(this),
                name = this.name.substring('typelist-site['.length, this.name.length - 1),
                val = $input.val();
                if (name == 'body_bg_img') val = t4_site_root_url + val;
                if (name.match(/_font_weight/)){
                    if(val && val.match(/i/)){
                        val = val.replace("i","");
                        params[name.replace('_weight','_style')] = 'italic';
                    }else if(val && !val.match(/i/)){
                        params[name.replace('_weight','_style')] = 'normal';
                    }else{
                        params[name.replace('_weight','_style')] = 'inherit';
                    }
                }
                //if(name.match(/letter_spacing/) && val != 'normal') val = val+"px";
                params[name] = val;
        });
        root_css.push("}");
        root_Lighten_Darken.push("}");

        var coreCss = compileCss(cssTplStyle, params);
        // update init lighten darken base color
        coreCss += "\n"+root_css.join("\n");
        coreCss += "\n"+root_Lighten_Darken.join("\n");

        addCss(coreCss, 'core');

        // compile palette css
        var pattern_css = '';

        //var palettes = $('[name="jform[params][system_palettes]"]').val();
        var palettes = getPalettes();
        // update alias
        if (palettes) {
            for (var pname in palettes) {
                var palette = $.extend(true, {}, palettes[pname]);
                
                for (var name in palette) {
                    var val = palette[name];
                    if (name.match(/_color$/) && !val.match(/^(#|rgb)/)) {
                        val = val.replace(/\s/g, '-').replace(/_/g, '-').toLowerCase();
                
                        if(val.match(/user/)){
                            val = val.replace(/user-/g, '').toLowerCase();
                        }
                        palette[name] = 'var(--' + val + ')';
                    }
                }
                palette['class'] = 't4-palette-' + pname.toLowerCase();
                pattern_css += compileCss(cssTplPalette, palette) + '\n';
            }
            addCss(pattern_css, 'pattern');
        }

        loadFonts();
        // remove preview css
        //$previewiframe.contents().find('link[href$="' + tempId + '-preview.css"]').remove();
    }


    var getPalettes = function() {
        var palettes = {};
        $('.group_palette .pattern-list > .pattern').each(function(i, el) {
            var data = $(el).data();
            if (!data.title) return;
            if (!data.class) data.class = data.title.toLowerCase().replace(/\s/g, ' ');
            palettes[data.class] = data;
        })
        return palettes;
    }


    var loadFonts = function() {

        var $fonts = $('[id$="_font_family"]'),
            fonts = [];

        $('[id$="_font_family"]').each(function() {
            var $font = $(this),
                family = $font.val(),
                baseid = this.id.replace(/^(.*)_family$/, '$1'),
                weight = $('#' + baseid + '_weight').val(),
                load_weight = $font.data('loadweights'),
                isGoogle = $font.data('fonttype') == 'google';

            if (isGoogle) {
                var weights = load_weight ? load_weight.split(',') : [];
                weights.push(weight ? weight : 400);
                weights = weights.filter(function(v, i, a) {return a.indexOf(v) === i});
                if(family && family != 'inherit') fonts.push(family + ':' + weights.join(','));
            }
        })
        let tofFont = $("#typelist_theme_dont_use_google_font").val();
        // remove current
        if (!tofFont && fonts.length) {
            // add new
            addStylesheet('https://fonts.googleapis.com/css?family=' + fonts.join('|'), 'google-font');
        }

    }


    var compileCss = function(css_tpl, params) {

        css_tpl = css_tpl.replace(/\{/g, '{\n');
        css_tpl = css_tpl.replace(/\}/g, '\n}');
        var arr = css_tpl.split('\n');

        var result = [];
        var re = /__([0-9a-z_]+)/gi;
        for (var i = 0; i < arr.length; i++) {
            var s = arr[i].trim();
            if (!s) continue;

            var match;
            var replaced = false, found = false;
            while (match = re.exec(s)) {
                found = true;
                var name = match[1],
                    val = params[name];

                if (val) {
                    s = s.replace(match[0], val);
                    replaced = true;
                }
            }

            if (replaced || !found) {
                result.push(s);
            }

        }
        css = result.join('\n');
        return css;
    }

    var addStylesheet = function(url, name) {
        var $preview_doc = $previewiframe.contents(),
            id = 'custom-stylesheet-' + name,
            $preview_style = $preview_doc.find('#' + id);
        if ($preview_style.length) {
            // replace content
            $preview_style.replaceWith('<link id="' + id + '" href="' + url + '" rel="stylesheet" type="text/css">');
        } else {
            $preview_doc.find('head').append('<link id="' + id + '" href="' + url + '" rel="stylesheet" type="text/css">');
        }
    }

    var addCss = function(css, name) {
        var $preview_doc = $previewiframe.contents(),
            id = 'custom-style-' + name,
            $preview_style = $preview_doc.find('#' + id);
        if ($preview_style.length) {
            // replace content
            $preview_style.replaceWith('<style id="' + id + '">' + css + '</style>');
        } else {
            // insert before custom css
            var $customcss = $preview_doc.find('link[href*="custom.css"]');
            var $newcss = $('<style id="' + id + '">' + css + '</style>');
            if ($customcss.length) {
                $newcss.insertBefore($customcss);
            } else {
                $preview_doc.find('head').append($newcss);
            }
        }
    }


    var postdataTimeout = {};
    var postDraft = function(type, data) {
        // if (!lastPostData[type]) {
        //     lastPostData[type] = JSON.stringify(data);
        //     return;
        // }
        // if (type != 'layout' && lastPostData[type] == JSON.stringify(data)) return;
        // lastPostData[type] = JSON.stringify(data);

        // using timeout
        if (postdataTimeout[type])
            clearTimeout(postdataTimeout[type]);

        postdataTimeout[type] = setTimeout(function() {
            // save draft data
            var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=draft&id=' + tempId;
            $.post(url, { task: 'save', type: type, data: data }).then(function(response) {
                if (response.ok) {
                    reloadPreview();
                } else {
                    T4Admin.Messages(response.error, 'error');
                }
            })
        }, 100);

    }


    var init = function() {

        $previewiframe = $('#custom-style-preview iframe');
        previewiframe = $previewiframe.get(0);

        t4_site_root_url = window.t4_site_root_url;
        cssTplStyle = window.cssTplStyle || '';
        cssTplPalette = window.cssTplPalette || '';
        t4previewkey = window.t4previewkey || '';
        tempId = window.tempId || '';

        // register events
        $previewiframe.on('load', function(e) {
            // Event for link click
            $previewiframe.contents().on('click', 'a', function(e) {
                    e.preventDefault();
                    reloadPreview(this.href);
                    return false;
                })
                // store for next view
            var previewurl = previewiframe.contentWindow.location.href;
            updatePreviewLink(previewurl);
            // reload assets
            clearTimeout(assetTimeout);
            assetTimeout = setTimeout(reloadAssets, 50);
        });

        //
        //$(document).on('reload-preview', reloadPreview);
        $(document).on('reload-assets', reloadAssets);

        var assetTimeout = 0;
        $('body').on('change', '[name^="jform[params][styles_"]', reloadAssets);
        $('input.custom-color-item, [name^="typelist-theme["]').on('change', function(e) {
            clearTimeout(assetTimeout);
            assetTimeout = setTimeout(reloadAssets, 50);
        })

        $('[name^="typelist-navigation["]').on('change', function(e) {
            var $this = $(e.target);
            var typelist = $this.closest('.typelist').data('typelist');
            if (!typelist.editting) return;
            postDraft('navigation', typelist.val());
        })
        $('[name^="typelist-site["]').on('change', function(e) {
            var $this = $(this);
            var typelist = $this.closest('.typelist').data('typelist');
            if (!typelist.editting) return;
            postDraft('site', typelist.val());
        })

        $(document).on('click', '.reload-preview', function () {
            removePreviewLink();
            reloadPreview();
        })

        $('#t4-preview-layout').on('change', function(e) { reloadPreview() });

        $('input.t4-layouts').on('change', function(e) {
            var $this = $(e.target);
            var typelist = $this.closest('.typelist').data('typelist');
            if (!typelist.editting) return;
            postDraft('layout', typelist.val());
        });

        $('.t4-draft-preview').on('change', function(e) {
            var $this = $(this);
            postDraft('sub-layout', $this.val());
        });
    }

    $(document).ready(init);
    $(window).on('load', function() {
        reloadPreview();
    });
    $(document).on('typelist.load', function (e, typelist) {
        postDraft(typelist.type, typelist.val());
    });
})(jQuery)
