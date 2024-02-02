<?php
namespace App\HTML;

class Form {
    
    private $data;

    private $errors;

    public function __construct($data, array $errors)
    {
        $this->data = $data;
        $this->errors = $errors;
    }

    public function input (string $key, string $label): string
    {
        // Obtient la valeur du champ spécifié par la clé
        $value = $this->getValue($key);
        // Détermine le type d'input en fonction de la clé (si c'est un champ de mot de passe, utilise "password", sinon "text")
        $type = $key === "password" ? "password" : "text"; 
        // Utilise la syntaxe HEREDOC pour générer le code HTML
        return <<<HTML
          <div class="form-group">
            <label for="field{$key}">{$label}</label>
            <input type="{$type}" id="field{$key}" class="{$this->getInputClass($key)}" name="{$key}" value="{$value}" required>
            {$this->getErrorFeedback($key)}
        </div>
HTML;
    }

  
    public function textarea (string $key, string $label): string
    {
        $value = $this->getValue($key);
        return <<<HTML
          <div class="form-group">
            <label for="field{$key}">{$label}</label>
            <textarea type="text" id="field{$key}" class="{$this->getInputClass($key)}" name="{$key}" required>{$value}</textarea>
            {$this->getErrorFeedback($key)}
        </div>
HTML;
    }

   
    public function select (string $key, string $label, array $options = []): string
    {
        // Initialise un tableau pour stocker le code HTML des options
        $optionsHTML = [];
        // Obtient la valeur du champ spécifié par la clé
        $value = $this->getValue($key);
        // Boucle à travers les options fournies
        foreach($options as $k => $v) {

            // Vérifie si l'option est sélectionnée en fonction de la valeur du champ
            $selected = in_array($k, $value) ? " selected" : "";
              // Ajoute le code HTML de l'option au tableau des options
            $optionsHTML[] = "<option value=\"$k\"$selected>$v</option>";

        }

         // Fusionne le tableau des options en une chaîne de caractères
        $optionsHTML = implode('', $optionsHTML);
         // Utilise la syntaxe HEREDOC pour générer le code HTML
        return <<<HTML
          <div class="form-group">
            <label for="field{$key}">{$label}</label>
            <select id="field{$key}" class="{$this->getInputClass($key)}" name="{$key}[]" required multiple>{$optionsHTML}</select>
            {$this->getErrorFeedback($key)}
        </div>
HTML;
    }

    
    private function getValue (string $key)
    {
        // Vérifie si les données sont stockées dans un tableau
        if (is_array($this->data)) {
            // Retourne la valeur associée à la clé dans le tableau, ou null si la clé n'existe pas
            return $this->data[$key] ?? null;
        }

        // Si les données ne sont pas un tableau, cela suppose qu'elles sont stockées dans un objet

        // Génère le nom de la méthode pour obtenir la valeur du champ
        $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

        // Appelle la méthode générée pour obtenir la valeur du champ à partir de l'objet
        $value = $this->data->$method(); 

        // Si la valeur est une instance de DateTimeInterface
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

          // Retourne la valeur obtenue
        return $value;
    }

   
    private function getInputClass (string $key): string 
    {
        // Initialise la classe de base du champ de formulaire
        $inputClass = 'form-control';

        // Ajoute la classe 'is-invalid' si des erreurs sont associées à la clé
        if (isset($this->errors[$key])) {
            $inputClass .= ' is-invalid';
        }
        // Retourne la chaîne de classes CSS résultante
        return $inputClass;
    }

    private function getErrorFeedback (string $key): string
    {
         // Vérifie si des erreurs sont associées à la clé
        if (isset($this->errors[$key])) { // si c'est un tableau d'erreur, on utilise implode
            
            // Si c'est un tableau d'erreur, utilise implode pour les concaténer avec des sauts de ligne HTML
            if (is_array($this->errors[$key])) {
                $error = implode('<br>', $this->errors[$key]);
            } else {  
                 // Si c'est une seule erreur, utilise directement cette erreur
                $error = $this->errors[$key];
            }

            // Retourne un message d'erreur formaté avec la classe CSS 'invalid-feedback'
            return '<div class="invalid-feedback">' . $error . '</div>';
        }

         // Retourne une chaîne vide si aucune erreur n'est associée à la clé
        return '';
    }

}