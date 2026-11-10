<?php
/**
 * Plugin Name: Ardensa Mailer
 * Description: Creación de cuentas y recuperación de contraseña usando WooCommerce.
 * Version: 2.0
 * Author: Soluciones Ardensa
 */

defined('ABSPATH') || exit;

class Ardensa_Mailer {

    public function __construct() {

        add_action('wp_ajax_nopriv_ardensa_crear_cuenta', [$this,'crear_cuenta']);
        add_action('wp_ajax_nopriv_ardensa_recuperar_password', [$this,'recuperar_password']);
        add_action('wp_ajax_ardensa_recuperar_password', [$this,'recuperar_password']);

    }

    /**
     * ============================================
     * CREAR CUENTA
     * ============================================
     */

    public function crear_cuenta(){

        check_ajax_referer('mitienda_nonce','nonce');

        $email = sanitize_email($_POST['email']);

        if(empty($email)){
            wp_send_json_error([
                'message'=>'Debe ingresar un correo electrónico.'
            ]);
        }

        if(!is_email($email)){
            wp_send_json_error([
                'message'=>'Correo electrónico inválido.'
            ]);
        }

        if(email_exists($email)){
            wp_send_json_error([
                'message'=>'Ya existe una cuenta con este correo.'
            ]);
        }

        // WooCommerce genera usuario automáticamente
        $username = sanitize_user(current(explode('@',$email)), true);

        $base = $username;
        $i = 1;

        while(username_exists($username)){
            $username = $base.$i;
            $i++;
        }

        // Contraseña temporal (el usuario luego define la suya)
        $password = wp_generate_password(32);

        $user_id = wc_create_new_customer(
            $email,
            $username,
            $password
        );

        if(is_wp_error($user_id)){

            wp_send_json_error([
                'message'=>$user_id->get_error_message()
            ]);

        }

        /**
         * Envía el correo "Nueva cuenta"
         */

        if(function_exists('WC')){

            $mailer = WC()->mailer();

            $emails = $mailer->get_emails();

            if(isset($emails['WC_Email_Customer_New_Account'])){

                $emails['WC_Email_Customer_New_Account']->trigger(
                    $user_id,
                    $password,
                    true
                );

            }

        }

        wp_send_json_success([
            'message'=>'Cuenta creada correctamente. Revisa tu correo para establecer tu contraseña.'
        ]);

    }


    /**
     * ============================================
     * RECUPERAR CONTRASEÑA
     * ============================================
     */

    public function recuperar_password(){

        check_ajax_referer('mitienda_nonce','nonce');

        $email = sanitize_email($_POST['email']);

        if(!is_email($email)){

            wp_send_json_error([
                'message'=>'Correo inválido.'
            ]);

        }

        $user = get_user_by('email',$email);

        if(!$user){

            wp_send_json_error([
                'message'=>'No existe una cuenta con ese correo.'
            ]);

        }

        retrieve_password($user->user_login);

        wp_send_json_success([
            'message'=>'Te enviamos un enlace para restablecer tu contraseña.'
        ]);

    }

}

new Ardensa_Mailer();