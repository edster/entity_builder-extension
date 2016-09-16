<?php

/**
 * Builder extension helper functions
 *
 * @link      http://websemantics.ca/ibuild
 * @link      http://ibuild.io
 * @author    WebSemantics, Inc. info@websemantics.ca
 * @author    Adnan Sagar msagar@websemantics.ca
 * @copyright 2012-2016 Web Semantics, Inc.
 * @since     March 24th 2015
 */

 /**
  * Render a string view template
  *
  * @param    string $template, view template
  * @param    object/array $context, values
  * @return   string
  */

  if (!function_exists('_render')) {
    function _render($template, $context)
    {
      return (new \Twig_Environment(new \Twig_Loader_String))->render($template, $context);
    }
  }

  /**
   * Shorthand for config helper
   *
   * @param    string $path, config path
   * @return   *,
   */

   if (!function_exists('_config')) {
     function _config($path)
     {
       return config('websemantics.extension.builder::' . $path);
     }
   }

  /**
   * Shorthand for view helper
   *
   * @param    string $path, view path
   * @return   Illuminate\View\View
   */

   if (!function_exists('_view')) {
     function _view($path)
     {
       return view('websemantics.extension.builder::' . $path);
     }
   }

  /**
   * Resolve a namespace to its parts, {vendor}.{type}.{slug} and the addon {path}
   *
   * @param    string $namespace
   * @param    boolean $shared
   * @return   array [$vendor, $type, $slug, $path]
   */

  if (!function_exists('_resolveAddonNamespace')) {
    function _resolveAddonNamespace($namespace, $shared)
    {
      $shared = $shared ? 'shared' : app('Anomaly\Streams\Platform\Application\Application')->getReference();

      if (!str_is('*.*.*', $namespace)) {
          throw new \Exception("The namespace should be snake case and formatted like: {vendor}.{type}.{slug}");
      }

      list($vendor, $type, $slug) = array_map(
          function ($value) {
              return str_slug(strtolower($value), '_');
          },
          explode('.', $namespace)
      );

      $type = str_singular($type);

      return [$vendor, $type, $slug,  base_path("addons/{$shared}/{$vendor}/{$slug}-{$type}")];
    }
  }

  /**
   * Return a true if the array provided is associative
   *
   * @param    array $array
   * @return   boolean
   */

   if (!function_exists('_isAssociative')) {
     function _isAssociative($array)
     {
         return ($array !== array_values($array));
     }
   }

 /**
  * Return a list of namespaces from the provided module's build config file,
  *
  * @param    Module - $module
  * @return   array
  */

  if (!function_exists('_getNamespaces')) {
      function _getNamespaces($module){
        $namespaces = array_get(config($module->getNamespace('builder')), 'namespaces', []);
        return _isAssociative($namespaces) ? array_keys($namespaces) : $namespaces;
      }
  }

/**
  * Return field config params, {hide column}, {form field}, {view template} etc
  *
  * @param    Module - $module
  * @param    String - $namespace_slug
  * @param    String - $field_slug
  * @return   array [$hide_column, $hide_field, $column_template]
  */

  if (!function_exists('_getFieldConfig')) {
      function _getFieldConfig($module, $namespace_slug, $field_slug){
        $namespaces = array_get(config($module->getNamespace('builder')), 'namespaces', []);
        $namespace = array_get($namespaces, $namespace_slug, []);
        $field = array_get($namespace, $field_slug, []);

        return [
          'hide_column' => array_get($field, 'hide_column', false),
          'hide_field' => array_get($field, 'hide_field', false),
          'column_template' => array_get($field, 'column_template', null),
        ];
      }
  }

 /**
  * Return the folder name for the curent namespace or empty
  *
  * @param    Module - $module
  * @param    String - $namespace
  * @param    Bollean - $forward, if true, use forward slash, otherwise backward slash
  * @return   string
  */

  if (!function_exists('_getNamespaceFolder')) {
      function _getNamespaceFolder($module, $namespace, $forward = false){
        $namespace = ($forward) ? "$namespace/": "$namespace\\";

        $namespaces = array_get(config($module->getNamespace('builder')), 'namespaces', []);

        return array_get(config($module->getNamespace('builder')),
                            'namespace_folder', true) ? $namespace : "";
      }
  }

 /**
  * Return the folder template for the curent namespace
  *
  * @param    Module - $module
  * @return   string
  */

  if (!function_exists('_getNamespaceFolderTemplate')) {
      function _getNamespaceFolderTemplate($module){
        return array_get(config($module->getNamespace('builder')),
                            'namespace_folder', true) ? "" : "{namespace}/";
      }
  }

 /**
  * Return a list of class suffixes that won't be overwritten
  *
  * @param    Module - $module
  * @return   string
  */

  if (!function_exists('_getAvoidOverwrite')) {
      function _getAvoidOverwrite($module, $extra = []){
        return array_merge(array_get(config($module->getNamespace('builder')),
                            'avoid_overwrite', []),$extra);
      }
  }

 /**
  * Return the seeding option ('yes' or 'no' *default)
  *
  * @param    Module - $module
  * @return   string
  */

  if (!function_exists('_seedingOption')) {
      function _seedingOption($module){
        return array_get(config($module->getNamespace('builder')), 'seeding', 'no');
      }
  }

 /**
  * Return the DocBlock from the module's build config file
  *
  * @param    Module - $module
  * @return   string
  */

  if (!function_exists('_getDocblock')) {
      function _getDocblock($module){
        return array_get(config($module->getNamespace('builder')),
                            'docblock', '');
      }
  }

 /**
  * Return a string to display or a null relationship entry value
  *
  * @return   string
  */

  if (!function_exists('_nullRelationshipEntry')) {
      function _nullRelationshipEntry($module){
        return array_get(config($module->getNamespace('builder')),
                            'null_relationship_entry',
                            '<span class="label label-default">null</span>');
      }
  }

 /**
  * Get the 'extends' string for a possible Super Class for the Entity Repository
  *
  * @return   string
  */

  if (!function_exists('_extendsRepository')) {
      function _extendsRepository($module){
        $extends_repository = array_get(config($module->getNamespace('builder')),
                            'extends_repository', '');

        if($extends_repository) {
          $extends_repository = explode('\\', $extends_repository);
          return (!empty($extends_repository))?"extends " . end($extends_repository) . " ":"";
        }

        return '';
      }
  }

 /**
  * Get the 'use' string for the parent class that Entity Repository extends
  *
  * @return   string
  */

  if (!function_exists('_extendsRepositoryUse')) {
      function _extendsRepositoryUse($module){
        $extends_repository =  array_get(config($module->getNamespace('builder')),
                            'extends_repository',null);
        return (!empty($extends_repository))? "use $extends_repository;":"";
      }
  }

 /**
  * Extract the class name of the assignment filed type, i.e. TextFieldType to
  * access the field template i.e. 'templates/field/table/TextFieldType.txt'
  * If the 'column_template' is set, use 'templates/field/table/template/TextFieldType.txt'
  *
  * @param  AssignmentModel $assignment
  * @return   string
  */

  if (!function_exists('_getFieldTypeClassName')) {
      function _getFieldTypeClassName($assignment){
        $fieldTypeClassName = get_class($assignment->getFieldType());
        return substr($fieldTypeClassName,strrpos($fieldTypeClassName, '\\') + 1);
      }
  }
