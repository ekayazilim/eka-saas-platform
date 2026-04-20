<?php

namespace Core;

class EkaValidator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            $ruleArray = explode('|', $ruleString);
            
            foreach ($ruleArray as $rule) {
                if ($rule === 'required' && empty($value)) {
                    $this->addError($field, "{$field} alanı zorunludur.");
                }
                
                if ($rule === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "Lütfen geçerli bir e-posta adresi girin.");
                }
                
                if (str_starts_with($rule, 'min:') && !empty($value)) {
                    $min = (int)str_replace('min:', '', $rule);
                    if (strlen($value) < $min) {
                        $this->addError($field, "{$field} en az {$min} karakter olmalıdır.");
                    }
                }
            }
        }
        
        return empty($this->errors);
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        if (empty($this->errors)) {
            return null;
        }
        
        $firstField = reset($this->errors);
        return reset($firstField);
    }
}
