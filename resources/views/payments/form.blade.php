<script>
    let pollingInterval = null;
    let mpesaModalInstance = null;
    let mpesaConfirmed = false;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap modal
        const modalElement = document.getElementById('mpesaModal');
        if (modalElement) {
            mpesaModalInstance = new bootstrap.Modal(modalElement);
        }

        const paymentMethodRadios = document.querySelectorAll('.payment-method-radio');
        const studentSelect = document.getElementById('student_id');
        const reopenPrompt = document.getElementById('reopenMpesaPrompt');
        const confirmedBanner = document.getElementById('mpesaConfirmedBanner');

        // Function to get current student name from data-name attribute
        function getCurrentStudentName() {
            if (!studentSelect || !studentSelect.value) return '— No student selected —';
            const option = studentSelect.options[studentSelect.selectedIndex];
            if (!option) return '— No student selected —';
            
            // Try to get from data-name attribute first
            const dataName = option.getAttribute('data-name');
            if (dataName && dataName.trim()) {
                return dataName.trim();
            }
            
            // Fallback: extract from text
            let text = option.text.trim();
            text = text.replace(/\s*\([^)]*\)\s*/g, ' ').trim();
            text = text.replace(/\s+/g, ' ').trim();
            return text || '— No student selected —';
        }

        // Function to get student phone
        function getStudentPhone() {
            if (!studentSelect || !studentSelect.value) return '';
            const option = studentSelect.options[studentSelect.selectedIndex];
            return option ? option.getAttribute('data-phone') || '' : '';
        }

        // Function to sync modal fields
        function syncModalFields() {
            const studentNameElement = document.getElementById('modal_student_name');
            const modalAmount = document.getElementById('modal_mpesa_amount');
            const phoneInput = document.getElementById('modal_mpesa_phone');
            const amountInput = document.getElementById('amount');
            
            // Update student name
            if (studentNameElement) {
                studentNameElement.textContent = getCurrentStudentName();
            }
            
            // Update amount
            if (modalAmount && amountInput) {
                modalAmount.value = amountInput.value || '0.00';
            }
            
            // Update phone
            const studentPhone = getStudentPhone();
            if (phoneInput && studentPhone) {
                phoneInput.value = studentPhone;
            }
            
            // Update hidden phone field
            const mpesaPhone = document.getElementById('mpesa_phone');
            if (mpesaPhone && studentPhone) {
                mpesaPhone.value = studentPhone;
            }
        }

        // When student changes
        if (studentSelect) {
            studentSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const phone = selectedOption?.getAttribute('data-phone') || '';
                
                // Update modal fields
                syncModalFields();
                
                // Update hidden fields
                const mpesaPhone = document.getElementById('mpesa_phone');
                if (mpesaPhone) mpesaPhone.value = phone;
            });
        }

        // When amount changes
        const amountInput = document.getElementById('amount');
        if (amountInput) {
            amountInput.addEventListener('input', function() {
                const modalAmount = document.getElementById('modal_mpesa_amount');
                if (modalAmount) {
                    modalAmount.value = this.value || '0.00';
                }
            });
        }

        // When payment method changes
        paymentMethodRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const isMpesa = this.getAttribute('data-mpesa') === 'true';

                if (isMpesa) {
                    mpesaConfirmed = false;
                    if (confirmedBanner) confirmedBanner.classList.add('d-none');
                    if (reopenPrompt) reopenPrompt.classList.remove('d-none');
                    
                    // Sync all fields before showing modal
                    syncModalFields();
                    
                    if (mpesaModalInstance) {
                        mpesaModalInstance.show();
                    }
                } else {
                    if (reopenPrompt) reopenPrompt.classList.add('d-none');
                    if (confirmedBanner) confirmedBanner.classList.add('d-none');
                }
            });
        });

        // Show modal if M-Pesa was previously selected (on page reload with errors)
        const mpesaRadio = document.querySelector('.payment-method-radio[data-mpesa="true"]');
        if (mpesaRadio && mpesaRadio.checked) {
            if (reopenPrompt) reopenPrompt.classList.remove('d-none');
            syncModalFields();
        }

        // Stop polling if the modal is dismissed before completion
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', function() {
                if (pollingInterval && !mpesaConfirmed) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
            });
        }

        // Also sync when modal is shown
        if (modalElement) {
            modalElement.addEventListener('show.bs.modal', function() {
                syncModalFields();
            });
        }

        // Auto-submit form if payment was already confirmed (after page reload)
        if (mpesaConfirmed) {
            const form = document.getElementById('paymentForm');
            if (form) {
                setTimeout(() => {
                    form.submit();
                }, 500);
            }
        }
    });

    // Quick amount buttons
    function quickAmount(amount) {
        const amountInput = document.getElementById('amount');
        const modalAmount = document.getElementById('modal_mpesa_amount');
        if (amountInput) {
            amountInput.value = amount;
            amountInput.focus();
            amountInput.dispatchEvent(new Event('input'));
        }
        if (modalAmount) modalAmount.value = amount;
    }

    // Validate M-Pesa number (in modal)
    function validateMpesaNumber() {
        const phone = document.getElementById('modal_mpesa_phone')?.value || '';
        const result = document.getElementById('modal_phoneValidationResult');
        const cleaned = phone.replace(/[^0-9]/g, '');

        if (cleaned.length >= 9 && cleaned.length <= 12) {
            if (result) {
                result.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Valid Mpesa number</span>';
            }
            return true;
        } else {
            if (result) {
                result.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle"></i> Enter a valid Mpesa number (9-12 digits)</span>';
            }
            return false;
        }
    }

    // Get CSRF token from meta tag
    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    // Initiate M-Pesa STK Push
    async function initiateMpesaPayment() {
        const studentId = document.getElementById('student_id')?.value;
        const amount = document.getElementById('amount')?.value;
        const phone = document.getElementById('modal_mpesa_phone')?.value;
        const reference = document.getElementById('mpesa_reference')?.value;

        if (!studentId) {
            Swal.fire({
                icon: 'warning',
                title: 'Student Required',
                text: 'Please select a student first.',
                confirmButtonColor: '#6c8cff'
            });
            return;
        }
        if (!amount || parseFloat(amount) <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Amount',
                text: 'Please enter a valid amount.',
                confirmButtonColor: '#6c8cff'
            });
            return;
        }
        if (!validateMpesaNumber()) {
            Swal.fire({
                icon: 'warning',
                title: 'Phone Number Required',
                text: 'Please enter a valid Mpesa phone number.',
                confirmButtonColor: '#6c8cff'
            });
            return;
        }

        // Update hidden phone field
        const mpesaPhone = document.getElementById('mpesa_phone');
        if (mpesaPhone) mpesaPhone.value = phone;

        const statusDiv = document.getElementById('modal_transactionStatus');
        if (statusDiv) statusDiv.classList.remove('d-none');
        
        const statusIcon = document.getElementById('modal_statusIcon');
        const statusTitle = document.getElementById('modal_statusTitle');
        const statusMessage = document.getElementById('modal_statusMessage');
        const statusProgress = document.getElementById('modal_statusProgress');
        const sendButton = document.getElementById('modal_sendStkPush');

        if (statusIcon) {
            statusIcon.style.background = '#ffc107';
            statusIcon.innerHTML = '<i class="fas fa-clock text-white"></i>';
        }
        if (statusTitle) statusTitle.textContent = 'Processing Payment';
        if (statusMessage) statusMessage.textContent = 'Sending STK Push to the phone...';
        if (statusProgress) statusProgress.style.width = '20%';
        if (sendButton) sendButton.disabled = true;

        try {
            const response = await axios.post('{{ route("mpesa.stkpush") }}', {
                student_id: studentId,
                amount: amount,
                phone: phone,
                reference: reference,
                _token: getCsrfToken()
            });

            if (response.data.success) {
                if (statusIcon) {
                    statusIcon.style.background = '#28a745';
                    statusIcon.innerHTML = '<i class="fas fa-check-circle text-white"></i>';
                }
                if (statusTitle) statusTitle.textContent = 'STK Push Sent!';
                if (statusMessage) {
                    statusMessage.textContent = 'Ask the payee to check their phone and enter their Mpesa PIN.';
                }
                if (statusProgress) statusProgress.style.width = '40%';

                const checkoutId = document.getElementById('mpesa_checkout_request_id');
                if (checkoutId) {
                    checkoutId.value = response.data.checkout_request_id;
                }
                
                // Start polling
                startPolling(response.data.checkout_request_id);
            } else {
                mpesaFailed(response.data.message || 'Failed to send payment request.');
            }
        } catch (error) {
            console.error('Mpesa Error:', error);
            const message = error.response?.data?.message || 'Failed to process payment request.';
            mpesaFailed(message);
        }
    }

    // Handle M-Pesa failure
    function mpesaFailed(message) {
        const statusIcon = document.getElementById('modal_statusIcon');
        const statusTitle = document.getElementById('modal_statusTitle');
        const statusMessage = document.getElementById('modal_statusMessage');
        const statusProgress = document.getElementById('modal_statusProgress');
        const sendButton = document.getElementById('modal_sendStkPush');

        if (statusIcon) {
            statusIcon.style.background = '#dc3545';
            statusIcon.innerHTML = '<i class="fas fa-times-circle text-white"></i>';
        }
        if (statusTitle) statusTitle.textContent = 'Payment Failed';
        if (statusMessage) statusMessage.textContent = message;
        if (statusProgress) statusProgress.style.width = '100%';
        if (sendButton) sendButton.disabled = false;

        Swal.fire({
            icon: 'error',
            title: 'STK Push Failed',
            text: message,
            confirmButtonColor: '#dc3545'
        });
    }

    // Poll for transaction status
    function startPolling(checkoutRequestId) {
        let attempts = 0;
        const maxAttempts = 60; // Increased to 60 (2 minutes)

        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }

        pollingInterval = setInterval(async () => {
            attempts++;
            
            const statusProgress = document.getElementById('modal_statusProgress');
            if (statusProgress) {
                const progress = Math.min(40 + (attempts * 1), 95);
                statusProgress.style.width = progress + '%';
            }

            try {
                const response = await axios.get(`{{ route("mpesa.status") }}?checkout_request_id=${checkoutRequestId}`);

                console.log('Status Check Response:', response.data);

                // Check for successful payment (from database or API)
                if (response.data.success && response.data.status === 'completed') {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                    
                    // Payment successful
                    mpesaConfirmed = true;

                    const statusIcon = document.getElementById('modal_statusIcon');
                    const statusTitle = document.getElementById('modal_statusTitle');
                    const statusMessage = document.getElementById('modal_statusMessage');
                    const statusProgress = document.getElementById('modal_statusProgress');
                    const sendButton = document.getElementById('modal_sendStkPush');

                    if (statusIcon) {
                        statusIcon.style.background = '#28a745';
                        statusIcon.innerHTML = '<i class="fas fa-check-circle text-white"></i>';
                    }
                    if (statusTitle) statusTitle.textContent = 'Payment Successful!';
                    if (statusMessage) {
                        const receipt = response.data.mpesa_receipt_number || 'N/A';
                        statusMessage.textContent = `Payment of KES ${response.data.amount || '0'} received. Receipt: ${receipt}`;
                    }
                    if (statusProgress) statusProgress.style.width = '100%';

                    // Update form fields
                    const transCode = document.getElementById('mpesa_transaction_code');
                    const resultCode = document.getElementById('mpesa_result_code');
                    const statusSelect = document.getElementById('status');
                    const receiptNo = document.getElementById('receipt_no');
                    const confirmedReceipt = document.getElementById('mpesaConfirmedReceipt');

                    if (transCode) transCode.value = response.data.mpesa_receipt_number || '';
                    if (resultCode) resultCode.value = '0';
                    if (statusSelect) statusSelect.value = 'paid';
                    if (receiptNo && response.data.mpesa_receipt_number) {
                        receiptNo.value = response.data.mpesa_receipt_number;
                    }
                    if (confirmedReceipt) confirmedReceipt.textContent = response.data.mpesa_receipt_number || 'N/A';

                    if (sendButton) sendButton.disabled = false;

                    // Close modal and show success with receipt
                    setTimeout(() => {
                        if (mpesaModalInstance) mpesaModalInstance.hide();
                        const reopenPrompt = document.getElementById('reopenMpesaPrompt');
                        const confirmedBanner = document.getElementById('mpesaConfirmedBanner');
                        if (reopenPrompt) reopenPrompt.classList.add('d-none');
                        if (confirmedBanner) confirmedBanner.classList.remove('d-none');
                        
                        // Show success message with receipt details
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Successful!',
                            html: `
                                <div style="text-align: left; margin: 10px 0;">
                                    <p><strong>Amount:</strong> KES ${response.data.amount || '0'}</p>
                                    <p><strong>Receipt Number:</strong> <span class="badge bg-success">${response.data.mpesa_receipt_number || 'N/A'}</span></p>
                                    <p><strong>Transaction Date:</strong> ${new Date().toLocaleString()}</p>
                                    <p><strong>Status:</strong> <span class="badge bg-success">Completed</span></p>
                                </div>
                                <hr>
                                <p class="text-muted small">Click "View Receipt" to see full details</p>
                            `,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'View Receipt',
                            showCancelButton: true,
                            cancelButtonColor: '#6c757d',
                            cancelButtonText: 'Record Payment'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to receipt page
                                const paymentId = response.data.payment_id || 0;
                                window.location.href = `{{ route('fees.receipt', '') }}/${paymentId}`;
                            } else {
                                // Submit the form to record payment
                                document.getElementById('paymentForm').submit();
                            }
                        });
                    }, 1000);

                    return;
                }

                // Check for failure
                if (response.data.success === false && response.data.status === 'failed') {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                    
                    const errorMessage = response.data.resultDesc || 'Payment failed. Please try again.';
                    mpesaFailed(errorMessage);
                    return;
                }

                // Check if we've reached max attempts
                if (attempts >= maxAttempts) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                    
                    const sendButton = document.getElementById('modal_sendStkPush');
                    if (sendButton) sendButton.disabled = false;
                    
                    const statusIcon = document.getElementById('modal_statusIcon');
                    const statusTitle = document.getElementById('modal_statusTitle');
                    const statusMessage = document.getElementById('modal_statusMessage');
                    const statusProgress = document.getElementById('modal_statusProgress');

                    if (statusIcon) {
                        statusIcon.style.background = '#ffc107';
                        statusIcon.innerHTML = '<i class="fas fa-clock text-white"></i>';
                    }
                    if (statusTitle) statusTitle.textContent = 'Status Unknown';
                    if (statusMessage) {
                        statusMessage.textContent = 'Still waiting for confirmation. Check Mpesa messages.';
                    }
                    if (statusProgress) statusProgress.style.width = '100%';

                    // Check if payment was actually successful by checking M-Pesa messages
                    Swal.fire({
                        icon: 'info',
                        title: 'Payment Status Unknown',
                        text: 'We are still waiting for confirmation. Please check your M-Pesa messages for the transaction.',
                        confirmButtonColor: '#6c8cff',
                        showCancelButton: true,
                        cancelButtonColor: '#28a745',
                        cancelButtonText: 'I Have Received Confirmation',
                        confirmButtonText: 'Wait Longer'
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            // User clicked "I Have Received Confirmation"
                            // Submit the form to record payment (will check if paid)
                            document.getElementById('paymentForm').submit();
                        }
                    });
                }
            } catch (error) {
                console.error('Polling Error:', error);
                if (attempts >= maxAttempts) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                    const sendButton = document.getElementById('modal_sendStkPush');
                    if (sendButton) sendButton.disabled = false;
                }
            }
        }, 2000);
    }

    // Form submission guard
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('paymentForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
                const status = document.getElementById('status')?.value;

                if (paymentMethod && paymentMethod.value === 'Mpesa' && status === 'pending') {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pending Mpesa Payment',
                        text: 'The Mpesa payment has not been confirmed yet. Send the STK push and wait for confirmation, or continue anyway.',
                        showCancelButton: true,
                        confirmButtonColor: '#6c8cff',
                        cancelButtonColor: '#dc3545',
                        confirmButtonText: 'Continue Anyway',
                        cancelButtonText: 'Send STK Push'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        } else {
                            if (mpesaModalInstance) mpesaModalInstance.show();
                        }
                    });
                }
            });
        }
    });
</script>