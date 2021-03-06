<?php

/**
 * Class AdminType
 * List of types:
 *      text
 *      html
 *      int
 *      float
 *      price
 *      password
 *      image
 *      file
 *      email
 *      date
 *      datetime
 *      bool
 *      select
 *      multiple
 *      hidden
 *      photos
 *      keyval
 *      items
 *      gps
 */
class                           AdminType {
    public static function      process_hidden($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                return $value;
                break;
            case 'preview':
                return $value;
                break;
            case 'edit':
                return '<input type="hidden" name="'.$key.'" value="'.str_replace('"', '&quot;', $value).'" />';
                break;
            case 'save':
                return $value;
                break;
        }
    }

    public static function      process_int($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                return $value;
                break;
            case 'preview':
                return $value;
                break;
            case 'edit':
                return '<input id="field_'.$key.'" type="text" name="'.$key.'" value="'.str_replace('"', '&quot;', $value).'" />';
                break;
            case 'save':
                return intval($value);
                break;
        }
    }

    public static function      process_float($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                return $value;
                break;
            case 'preview':
                return $value;
                break;
            case 'edit':
                return '<input id="field_'.$key.'" type="text" name="'.$key.'" value="'.str_replace('"', '&quot;', $value).'" />';
                break;
            case 'save':
                return floatval(str_replace([',', ' '], ['.', ''], $value));
                break;
        }
    }

    public static function      process_price($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                return number_format(floatval($value), 2, ',', ' ');
                break;
            case 'preview':
                return number_format(floatval($value), 2, ',', ' ');
                break;
            case 'edit':
                return '<input id="field_'.$key.'" type="text" name="'.$key.'" value="'.str_replace('"', '&quot;', $value).'" />';
                break;
            case 'save':
                return floatval(str_replace([',', ' '], ['.', ''], $value));
                break;
        }
    }

    public static function      process_text($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                return $value;
                break;
            case 'preview':
                return $value;
                break;
            case 'edit':
                return '<input id="field_'.$key.'" type="text" name="'.$key.'" value="'.str_replace('"', '&quot;', $value).'" />';
                break;
            case 'save':
                return $value;
                break;
        }
    }

    public static function      process_html($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                return $value;
                break;
            case 'preview':
                return $value;
                break;
            case 'edit':
                Conf::append('page.bottom', '<script type="text/javascript">
                    $(function(){
                        $("#field_'.$key.'").redactor({
                            imageUpload: "'.Argv::createUrl('admin').'?module=wysiwygImageUpload",
                            plugins: ["table", "fontsize", "fullscreen", "video"],
                            minHeight: 300,
                            maxHeight: 800,
                            lang: "'.Conf::get('lang').'"
                        });
                    });
                </script>');
                return '<textarea id="field_'.$key.'" name="'.$key.'" style="height: 350px;" class="text-left">'.$value.'</textarea>';
                break;
            case 'save':
                return $value;
                break;
        }
    }

    public static function      process_textarea($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                return $value;
                break;
            case 'preview':
                return $value;
                break;
            case 'edit':
                return '<textarea id="field_'.$key.'" name="'.$key.'" style="height: 350px;" class="text-left">'.$value.'</textarea>';
                break;
            case 'save':
                return $value;
                break;
        }
    }

    public static function      process_password($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                return '******';
                break;
            case 'preview':
                return '******';
                break;
            case 'edit':
                return '<input id="field_'.$key.'" type="password" name="'.$key.'" value="" autocomplete="off" placeholder="'._t("Laissez vide pour ne pas changer de mot de passe").'" />
                        <input type="password" name="'.$key.'_confirm" value="" autocomplete="off" placeholder="'._t("Confirmez").'" />';
                break;
            case 'save':
                if (trim($value) == '')
                    return null;
                if ($value != $_POST[$key.'_confirm'])
                    return null;
                return Hash::blowfish($value);
                break;
        }
    }

    public static function      process_email($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                return $value;
                break;
            case 'preview':
                return $value;
                break;
            case 'edit':
                return '<input id="field_'.$key.'" type="email" name="'.$key.'" value="'.str_replace('"', '&quot;', $value).'" />';
                break;
            case 'save':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL))
                    return null;
                return $value;
                break;
        }
    }

    public static function      process_gps($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                return '<img src="http://maps.googleapis.com/maps/api/staticmap?center='.$value.'&zoom=16&size=100x100&key='.Conf::get('keys.googlemaps').'" />';
                break;
            case 'preview':
                return '<img src="http://maps.googleapis.com/maps/api/staticmap?center='.$value.'&zoom=16&size=200x200&key='.Conf::get('keys.googlemaps').'" />';
                break;
            case 'edit':
            	$val = explode(',', $value);
            	if (sizeof($val) < 2)
            		$val[1] = 0;
				
				$val[0] = floatval($val[0]);
				$val[1] = floatval($val[1]);
				
				if ($val[0] == 0 && $val[1] == 0) {
					$val[0] = 48.85940229103392;
					$val[1] = 2.3381136274414303;
				}
				
                return '<input type="text" id="'.$key.'_autocomplete" placeholder="Rechercher une adresse ..." />
                    <div id="map_'.$key.'" style="height: 300px; width: 100%;"></div>
                	<div class="row">
                		<div class="small-6 column">
                			<input type="text" id="'.$key.'_lat" name="'.$key.'_lat" value="'.$val[0].'" placeholder="Latitude" onkeyup="update_'.$key.'_gps();" />
                		</div>
                		<div class="small-6 column">
                			<input type="text" id="'.$key.'_lng" name="'.$key.'_lng" value="'.$val[1].'" placeholder="Longitude" onkeyup="update_'.$key.'_gps();" />
                		</div>
                	</div>
                	<script type="text/javascript">
                		var '.$key.'_map = null;
                		var '.$key.'_marker = null;
                		var '.$key.'_autocomplete = null;
                		function create_'.$key.'_map() {
	                		var myLatlng = new google.maps.LatLng('.$val[0].', '.$val[1].');
							var mapOptions = {
							  zoom: 12,
							  center: myLatlng
							}
							'.$key.'_map = new google.maps.Map(document.getElementById("map_'.$key.'"), mapOptions);
							'.$key.'_autocomplete = new google.maps.places.Autocomplete(document.getElementById("'.$key.'_autocomplete"));
							'.$key.'_autocomplete.bindTo("bounds", '.$key.'_map);
							'.$key.'_marker = new google.maps.Marker({
							    position: myLatlng,
							    map: '.$key.'_map,
							    draggable: true
							});
							google.maps.event.addListener('.$key.'_marker, "dragend", function(event){
								var lat = event.latLng.lat();
								var lng = event.latLng.lng();
								$("#'.$key.'_lat").val(lat);
								$("#'.$key.'_lng").val(lng);
							});
							'.$key.'_autocomplete.addListener("place_changed", function(){
								var place = '.$key.'_autocomplete.getPlace();
								if (place.geometry) {
									'.$key.'_map.setCenter(place.geometry.location);
									'.$key.'_marker.setPosition(place.geometry.location);
									$("#'.$key.'_lat").val(place.geometry.location.lat());
									$("#'.$key.'_lng").val(place.geometry.location.lng());
								}
							});
	                	}
                	
						function update_'.$key.'_gps() {
							var lat = parseFloat($("#'.$key.'_lat").val());
							var lng = parseFloat($("#'.$key.'_lng").val());
							'.$key.'_map.setCenter(new google.maps.LatLng(lat, lng));
							'.$key.'_marker.setPosition(new google.maps.LatLng(lat, lng));
						}
						
                		google.maps.event.addDomListener(window, "load", create_'.$key.'_map);
                	</script>';
                break;
            case 'save':
            	return implode(',', [
	            	$_POST[$key.'_lat'],
	            	$_POST[$key.'_lng']
            	]);
                break;
        }
    }

    public static function      process_image($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                if (!strlen($value))
                    return '';
                return '<img src="'.$value.'" alt="" />';
                break;
            case 'preview':
                if (!strlen($value))
                    return '';
                return '<img src="'.$value.'" style="max-height: 32px; max-width: 32px;" alt="" />';
                break;
            case 'edit':
                return '<input id="field_'.$key.'" type="file" name="'.$key.'" style="margin-top: 9px" accept="image/*" /> <label><input type="checkbox" name="_delete_'.$key.'" value="1" /> Supprimer</label>';
                break;
            case 'save':
                if (isset($_POST['_delete_'.$key]))
                    return '';
                $value = Upload::job($key, false, ['jpg', 'jpeg', 'png', 'gif']);
            	if (!$value)
            		return null;
            	$i = new Image(ROOT.$value);
            	$i->exifRotation();
            	if ($params && preg_match('/^[0-9]+x[0-9]+$/', $params)) {
	            	list($width, $height) = explode('x', $params);
	            	$i->resize(intval($width), intval($height), false);
            	}
            	$i->save();
                return $value;
                break;
        }
    }

    public static function      process_file($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                return '<a href="'.$value.'" target="_blank">'._t("Accéder au fichier").'</a>';
                break;
            case 'preview':
                return '<a href="'.$value.'" target="_blank">'._t("Accéder au fichier").'</a>';
                break;
            case 'edit':
                return '<input id="field_'.$key.'" type="file" name="'.$key.'" style="margin-top: 9px" /> <label><input type="checkbox" name="_delete_'.$key.'" value="1" /> Supprimer</label>';
                break;
            case 'save':
                if (isset($_POST['_delete_'.$key]))
                    return '';
            	$value = Upload::job($key);
            	if (!$value)
            		return null;
                return $value;
                break;
        }
    }

    public static function      process_bool($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                return intval($value) == 1 ? '<span style="color: #43AC6A">'._t("Oui").'</span>' : '<span style="color: #F04124">'._t("Non").'</span>';
                break;
            case 'preview':
                return intval($value) == 1 ? '<span style="color: #43AC6A">'._t("Oui").'</span>' : '<span style="color: #F04124">'._t("Non").'</span>';
                break;
            case 'edit':
                return '<input id="field_'.$key.'" type="checkbox" name="'.$key.'" value="1"'.(intval($value) == 1 ? ' checked="checked"' : '').' style="margin-top: 14px" />';
                break;
            case 'save':
                return isset($_POST[$key]) ? 1 : 0;
                break;
        }
    }

    public static function      process_date($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                return date(_t("d/m/Y"), strtotime($value));
                break;
            case 'preview':
                return date(_t("d/m/Y"), strtotime($value));
                break;
            case 'edit':
                Conf::append('page.bottom', '<script type="text/javascript">
                    $(function(){
                        $("#field_'.$key.'").pickadate({
                            format: "'._t("dd/mm/yyyy").'",
                            formatSubmit: "yyyy-mm-dd"
                        });
                    });
                </script>');
                return '<input id="field_'.$key.'" type="text" name="'.$key.'" value="'.str_replace('"', '&quot;', date(_t("d/m/Y"), strtotime($value))).'" />';
                break;
            case 'save':
                return $_POST[$key.'_submit'];
                break;
        }
    }

    public static function      process_datetime($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                return date(_t("d/m/Y à H:i:s"), strtotime($value));
                break;
            case 'preview':
                return date(_t("d/m/Y à H:i:s"), strtotime($value));
                break;
            case 'edit':
                Conf::append('page.bottom', '<script type="text/javascript">
                    $(function(){
                        $("#field_'.$key.'").pickadate({
                            format: "'._t("dd/mm/yyyy").'",
                            formatSubmit: "yyyy-mm-dd"
                        });
                        $("#field_'.$key.'__time").pickatime({
                            format: "'._t("HH!hi").'",
                            formatSubmit: "HH:i:00"
                        });
                    });
                </script>');
                return '<div class="row">
                            <div class="small-7 medium-8 large-9 column">
                                <input id="field_'.$key.'" type="text" name="'.$key.'" value="'.str_replace('"', '&quot;', date(_t("d/m/Y"), strtotime($value))).'" />
                            </div>
                            <div class="small-5 medium-4 large-3 column">
                                <input id="field_'.$key.'__time" type="text" name="'.$key.'__time" value="'.str_replace('"', '&quot;', date(_t("H\hi"), strtotime($value))).'" />
                            </div>
                        </div>';
                break;
            case 'save':
                $value = $_POST[$key.'_submit'] . ' ' . $_POST[$key.'__time_submit'];
                return $value;
                break;
        }
    }

    public static function      process_select($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
                if (ArrayTools::isAssoc($params))
                    return isset($params[$value]) ? $params[$value] : '';
                return $value;
                break;
            case 'preview':
                if (ArrayTools::isAssoc($params))
                    return isset($params[$value]) ? $params[$value] : '';
                return $value;
                break;
            case 'edit':
                $str = '<select id="field_'.$key.'" name="'.$key.'">';
                foreach ($params as $k => $v) {
                    if (!ArrayTools::isAssoc($params))
                        $k = $v;
                    $str .= '<option value="'.str_replace('"', '&quot;', $k).'"'.($value == $k ? ' selected="selected"' : '').'>'.$v.'</option>';
                }
                $str .= '</select>';
                return $str;
                break;
            case 'save':
                return $value;
                break;
        }
    }

    public static function      process_multiple($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
				$value = explode(',', substr($value, 1, -1));
				$dis = [];
				foreach ($value as $v) {
		            if (ArrayTools::isAssoc($params))
		                $dis[] = isset($params[$v]) ? (is_array($params[$v]) ? $params[$v]['title'] : $params[$v]) : '';
                }
                return implode(', ', $dis);
                break;
            case 'preview':
                $value = explode(',', substr($value, 1, -1));
				$dis = [];
				foreach ($value as $v) {
		            if (ArrayTools::isAssoc($params))
		                $dis[] = isset($params[$v]) ? (is_array($params[$v]) ? $params[$v]['title'] : $params[$v]) : '';
                }
                return implode(', ', $dis);
                break;
            case 'edit':
            	$str = '<div class="row" style="padding-bottom: 40px;">';
            	$existant = explode(',', substr($value, 1, -1));
            	$cpt = 0;
            	foreach ($params as $k => $v) {
	            	if (!ArrayTools::isAssoc($params))
                        $k = $v;
                    $attr = '';
                    if (is_array($v) && isset($v['attr'])) {
	                    foreach ($v['attr'] as $a => $b)
	                    	$attr .= ' '.$a.'="'.str_replace('"', '&quot;', $b).'"';
                    }
                    $str .= '<div class="small-12 medium-6 large-4 column '.(++$cpt == sizeof($params) ? 'end' : '').'">
                    	<div class="row"'.$attr.'>
	                    	<div class="small-12 column" style="line-height: 40px;"><input type="checkbox" name="'.$key.'[]" value="'.str_replace('"', '&quot;', $k).'" '.(in_array($k, $existant) ? 'checked="checked"' : '').' id="cb_'.$key.'_'.Text::slug($k).'" /> <label for="cb_'.$key.'_'.Text::slug($k).'">'.(is_array($v) ? $v['title'] : $v).'</label></div>
	                    </div>
                    </div>';
            	}
            	$str .= '</div>';
                return $str;
                break;
            case 'save':
            	$val = [];
            	if (isset($_POST[$key]))
            		$val = $_POST[$key];
                return ','.implode(',', $val).',';
                break;
        }
    }
    
    public static function      process_items($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
				$value = explode(',', substr($value, 1, -1));
                return implode(', ', $value);
                break;
            case 'preview':
                $value = explode(',', substr($value, 1, -1));
                return implode(', ', $value);
                break;
            case 'edit':
            	$value = substr($value, 1, -1);
                return '<input type="text" id="field_'.$key.'" name="'.$key.'" value="'.str_replace('"', '&quot;', $value).'" /><script type="text/javascript">
                	$(function(){
	                	$("#field_'.$key.'").tagsInput({
		                	defaultText: "Saisissez",
		                	delimiter: ",",
		                	width: "100%"
	                	});
                	});
                </script>';
                break;
            case 'save':
                return ','.$_POST[$key].',';
                break;
        }
    }

    public static function      process_photos($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
            	$value = explode(';', $value);
            	$str = '';
            	foreach ($value as $photo) {
	            	$str .= '<img src="'.$photo.'" alt="" style="max-width:50px;max-height:50px;" />';
            	}
                return $str;
                break;
            case 'preview':
            	$value = explode(';', $value);
            	$str = '';
            	foreach ($value as $photo) {
	            	$str .= '<img src="'.$photo.'" alt="" style="max-width:50px;max-height:50px;" />';
            	}
                return $str;
                break;
            case 'edit':
            	$value = explode(';', $value);
            	$existing = [];
            	foreach ($value as $v) {
	            	if (!strlen($v))
	            		continue;
	            	$name = explode('/', $v);
	            	$name = $name[sizeof($name) - 1];
	            	$ext = explode('.', $name);
	            	$ext = $ext[sizeof($ext) - 1];
	            	
	            	$existing[] = [
		            	'name' => $name,
		            	'size' => filesize(ROOT.$v),
		            	'type' => 'image/'.$ext,
		            	'finalPath' => $v,
		            	'accepted' => true,
		            	'order' => sizeof($existing)
	            	];
            	}
            
            	return '<input type="hidden" name="'.$key.'" id="field_'.$key.'" value="'.implode(';', $value).'" />
            	<div id="'.$key.'_dz" class="dropzone" data-result="field_'.$key.'"></div>
            	<script type="text/javascript">
            		var '.$key.'Dz = null;
            		$(function(){
	            		'.$key.'Dz = new Dropzone("div#'.$key.'_dz", {
		            		url: "'.Argv::createUrl('admin').'?module=dzUpload&token=",
		            		addRemoveLinks: true,
		            		clickable: true,
		            		paramName: "file",
							maxFilesize: 8,
							init: function() {
								var existing = '.json_encode($existing).';
								for (var i = 0; i < existing.length; i++) {
							        this.emit("addedfile", existing[i]);
							        this.createThumbnailFromUrl(existing[i], existing[i].finalPath);
							        this.emit("success", existing[i]);
							        this.emit("complete", existing[i]);
							        this.files.push(existing[i]);
						        }
						        updateDzField(this);
							}
	            		});
	            		'.$key.'Dz.on("success", function(file, path){
		            		file.finalPath = path;
		            		updateDzField('.$key.'Dz);
	            		});
	            		'.$key.'Dz.on("removedfile", function(){
		            		updateDzField('.$key.'Dz);
	            		});
	            		$("#'.$key.'_dz").sortable({
					        items: ".dz-preview",
					        cursor: "move",
					        opacity: 0.5,
					        containment: "#'.$key.'_dz",
					        distance: 20,
					        tolerance: "pointer",
					        update: function(event, ui){
						        sortDz('.$key.'Dz);
					        }
					    });
            		});
            	</script>';
                break;
            case 'save':
                return $value;
                break;
        }
    }

    public static function      process_keyval($key, $value, $params, $mode) {
        switch ($mode) {
            case 'display':
				$value = json_decode($value, true);
				if (!$value)
					$value = [];
				$str = '';
				foreach ($value as $v)
					$str .= '<li><strong>'.$v['key'].'</strong>: '.$v['value'].'</li>';
				
                return "<ul>$str</ul>";
                break;
            case 'preview':
                $value = json_decode($value, true);
				if (!$value)
					$value = [];
				$str = '';
				foreach ($value as $v)
					$str .= '<li><strong>'.$v['key'].'</strong>: '.$v['value'].'</li>';
				
                return "<ul>$str</ul>";
                break;
            case 'edit':
            	$str = '';
            	$value = json_decode($value, true);
				if (!$value)
					$value = [];
				foreach ($value as $k => $v) {
					$str .= '<div class="row">
						<div class="small-5 column">
							<input type="text" name="'.$key.'['.$k.'][key]" value="'.str_replace('"', '&quot;', $v['key']).'" propname="key" placeholder="Nom" />
						</div>
						<div class="small-5 column">
							<input type="text" name="'.$key.'['.$k.'][value]" value="'.str_replace('"', '&quot;', $v['value']).'" propname="value" placeholder="Valeur" />
						</div>
						<div class="small-2 column">
							<a href="#" onclick="var el = this.parentNode.parentNode; if (el) {el.parentNode.removeChild(el);} reloadKeyvalIds(\''.$key.'\'); return false;"><i class="fi-x" style="font-size: 16px; color: red; margin-top: 10px; display: inline-block;"></i></a>
						</div>
					</div>';
				}
                return '<div id="keyval_'.$key.'_items" style="padding-top: 10px;">'.$str.'</div><div class="text-center" style="padding: 10px;"><a href="#" onclick="addLineToKeyval(\''.$key.'\'); return false;" class="button tiny">Ajouter</a></div>';
                break;
            case 'save':
            	$val = [];
            	if (isset($_POST[$key]))
            		$val = $_POST[$key];
                return json_encode($val);
                break;
        }
    }
}

?>