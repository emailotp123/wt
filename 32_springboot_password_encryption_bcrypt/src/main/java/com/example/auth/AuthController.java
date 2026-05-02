package com.example.auth;

import org.springframework.security.core.Authentication;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

@Controller
public class AuthController {
    private final UserRepository userRepo;
    private final PasswordEncoder passwordEncoder;

    public AuthController(UserRepository userRepo, PasswordEncoder passwordEncoder) {
        this.userRepo = userRepo;
        this.passwordEncoder = passwordEncoder;
    }

    @GetMapping("/register")
    public String registerPage(Model model) {
        model.addAttribute("user", new User());
        return "register";
    }

    @PostMapping("/register")
    public String register(@ModelAttribute User user, Model model) {
        if (userRepo.findByEmail(user.getEmail()).isPresent()) {
            model.addAttribute("error", "Email already registered.");
            return "register";
        }
        user.setPassword(passwordEncoder.encode(user.getPassword()));
        userRepo.save(user);
        return "redirect:/login?registered";
    }

    @GetMapping("/login")
    public String loginPage(@RequestParam(required = false) String registered, Model model) {
        if (registered != null) model.addAttribute("success", "Registered! Please login.");
        return "login";
    }

    @GetMapping("/dashboard")
    public String dashboard(Authentication auth, Model model) {
        userRepo.findByEmail(auth.getName()).ifPresent(u -> {
            model.addAttribute("user", u);
            model.addAttribute("hashedPassword", u.getPassword());
        });
        model.addAttribute("allUsers", userRepo.findAll());
        return "dashboard";
    }
}
