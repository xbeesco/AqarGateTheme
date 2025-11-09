/**
 * Property Payment System JavaScript
 * 
 * @package AqarGate
 * @version 1.0.0
 */

(function($) {
    'use strict';

    var PropertyPayment = {
        
        /**
         * تهيئة النظام
         */
        init: function() {
            this.bindEvents();
            this.setupUI();
        },

        /**
         * ربط الأحداث
         */
        bindEvents: function() {
            var self = this;
            
            // زر الدفع الرئيسي
            $(document).on('click', '.property-payment-btn', function(e) {
                e.preventDefault();
                self.handlePaymentClick($(this));
            });
            
            // زر إلغاء في نافذة التأكيد
            $(document).on('click', '.payment-modal-cancel', function() {
                self.closeModal();
            });
            
            // زر التأكيد في النافذة
            $(document).on('click', '.payment-modal-confirm', function() {
                self.processPayment();
            });
            
            // إغلاق النافذة بالضغط على ESC
            $(document).keyup(function(e) {
                if (e.keyCode === 27) {
                    self.closeModal();
                }
            });
        },

        /**
         * إعداد واجهة المستخدم
         */
        setupUI: function() {
            // إضافة تأثيرات hover للزر
            $('.property-payment-btn').hover(
                function() {
                    $(this).find('i').addClass('animated-icon');
                },
                function() {
                    $(this).find('i').removeClass('animated-icon');
                }
            );
        },

        /**
         * معالج النقر على زر الدفع
         */
        handlePaymentClick: function(button) {
            var self = this;
            
            // جلب البيانات من الزر
            this.currentButton = button;
            this.propertyId = button.data('property-id');
            this.price = button.data('price');
            this.type = button.data('type');
            this.nonce = button.data('nonce');
            
            // عرض نافذة التأكيد
            this.showConfirmationModal();
        },

        /**
         * عرض نافذة التأكيد
         */
        showConfirmationModal: function() {
            var self = this;
            
            // إنشاء محتوى النافذة
            var modalContent = this.createModalContent();
            
            // التحقق من وجود bootbox
            if (typeof bootbox !== 'undefined') {
                bootbox.dialog({
                    title: '<i class="houzez-icon icon-shopping-cart-1"></i> تأكيد عملية الدفع',
                    message: modalContent,
                    className: 'property-payment-modal',
                    buttons: {
                        cancel: {
                            label: '<i class="houzez-icon icon-close"></i> إلغاء',
                            className: 'btn-secondary',
                            callback: function() {
                                self.closeModal();
                            }
                        },
                        confirm: {
                            label: '<i class="houzez-icon icon-check"></i> تأكيد ومتابعة',
                            className: 'btn-primary',
                            callback: function() {
                                self.processPayment();
                                return false; // منع إغلاق النافذة تلقائياً
                            }
                        }
                    }
                });
            } else {
                // استخدام confirm بسيط كبديل
                if (confirm(property_payment_ajax.messages.confirm)) {
                    this.processPayment();
                }
            }
        },

        /**
         * إنشاء محتوى نافذة التأكيد
         */
        createModalContent: function() {
            var typeText = this.getTypeText(this.type);
            var formattedPrice = this.formatPrice(this.price);
            
            var content = '<div class="payment-confirmation-content">';
            content += '<div class="payment-summary">';
            content += '<h4>ملخص العملية</h4>';
            content += '<div class="summary-item">';
            content += '<span class="label">نوع العملية:</span>';
            content += '<span class="value">' + typeText + '</span>';
            content += '</div>';
            content += '<div class="summary-item">';
            content += '<span class="label">المبلغ المطلوب:</span>';
            content += '<span class="value price-highlight">' + formattedPrice + ' ريال سعودي</span>';
            content += '</div>';
            content += '</div>';
            
            content += '<div class="payment-notes">';
            content += '<p><i class="houzez-icon icon-lock-5"></i> جميع المعاملات المالية آمنة ومشفرة</p>';
            content += '<p><i class="houzez-icon icon-info-circle"></i> سيتم توجيهك لصفحة الدفع الآمنة</p>';
            content += '</div>';
            content += '</div>';
            
            return content;
        },

        /**
         * معالجة عملية الدفع
         */
        processPayment: function() {
            var self = this;
            
            // تعطيل الزر وعرض رسالة المعالجة
            this.setButtonLoading(true);
            
            // إعداد البيانات للإرسال
            var data = {
                action: 'process_property_payment',
                property_id: this.propertyId,
                nonce: this.nonce
            };
            
            // إرسال طلب AJAX
            $.ajax({
                url: property_payment_ajax.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        self.handleSuccess(response.data);
                    } else {
                        self.handleError(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    self.handleError({
                        message: property_payment_ajax.messages.error
                    });
                },
                complete: function() {
                    self.setButtonLoading(false);
                }
            });
        },

        /**
         * معالجة النجاح
         */
        handleSuccess: function(data) {
            var self = this;
            
            // إغلاق النافذة إن وجدت
            if (typeof bootbox !== 'undefined') {
                bootbox.hideAll();
            }
            
            // عرض رسالة النجاح
            this.showNotification('success', data.message || 'تم إنشاء الطلب بنجاح');
            
            // الانتظار قليلاً ثم التوجيه
            setTimeout(function() {
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                }
            }, 1500);
        },

        /**
         * معالجة الخطأ
         */
        handleError: function(data) {
            // إغلاق النافذة إن وجدت
            if (typeof bootbox !== 'undefined') {
                bootbox.hideAll();
            }
            
            // عرض رسالة الخطأ
            this.showNotification('error', data.message || property_payment_ajax.messages.error);
            
            // التحقق من ضرورة تسجيل الدخول
            if (data.redirect) {
                setTimeout(function() {
                    window.location.href = data.redirect;
                }, 2000);
            }
        },

        /**
         * تغيير حالة الزر (تحميل/عادي)
         */
        setButtonLoading: function(loading) {
            if (!this.currentButton) return;
            
            if (loading) {
                this.originalButtonText = this.currentButton.find('.btn-text').text();
                this.currentButton.prop('disabled', true);
                this.currentButton.find('.btn-text').html(
                    '<i class="houzez-icon icon-loader fa-spin"></i> ' + 
                    property_payment_ajax.messages.processing
                );
            } else {
                this.currentButton.prop('disabled', false);
                if (this.originalButtonText) {
                    this.currentButton.find('.btn-text').text(this.originalButtonText);
                }
            }
        },

        /**
         * عرض إشعار
         */
        showNotification: function(type, message) {
            // إزالة أي إشعارات سابقة
            $('.property-payment-notification').remove();
            
            // إنشاء الإشعار
            var notificationClass = type === 'success' ? 'alert-success' : 'alert-danger';
            var icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
            
            var notification = $('<div class="property-payment-notification alert ' + notificationClass + ' animated fadeInDown">');
            notification.html('<i class="houzez-icon icon-' + icon + '"></i> ' + message);
            
            // إضافة الإشعار للصفحة
            $('.property-payment-wrapper').prepend(notification);
            
            // إزالة الإشعار بعد 5 ثواني
            setTimeout(function() {
                notification.addClass('fadeOutUp');
                setTimeout(function() {
                    notification.remove();
                }, 1000);
            }, 5000);
        },

        /**
         * إغلاق النافذة المنبثقة
         */
        closeModal: function() {
            if (typeof bootbox !== 'undefined') {
                bootbox.hideAll();
            }
        },

        /**
         * الحصول على نص نوع العملية
         */
        getTypeText: function(type) {
            var types = {
                'sale': 'شراء العقار',
                'rent': 'إيجار العقار',
                'booking': 'حجز العقار',
                'commission': 'دفع العمولة',
                'inspection': 'معاينة العقار'
            };
            
            return types[type] || 'دفعة عقار';
        },

        /**
         * تنسيق السعر
         */
        formatPrice: function(price) {
            return parseFloat(price).toLocaleString('ar-SA', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    };

    // تشغيل النظام عند تحميل الصفحة
    $(document).ready(function() {
        PropertyPayment.init();
    });

    // تعريف النظام globally للوصول الخارجي
    window.PropertyPayment = PropertyPayment;

})(jQuery);