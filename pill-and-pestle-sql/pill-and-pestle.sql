-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 16, 2026 at 04:31 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pill-and-pestle`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Analgesics'),
(2, 'Antibiotics'),
(3, 'Antidepressants'),
(4, 'Antidiabetics'),
(5, 'Antiepileptics'),
(6, 'Antipsychotics'),
(7, 'Antivirals'),
(8, 'Anticoagulants'),
(9, 'Antihistamines');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `medicine_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`medicine_id`, `name`, `brand`, `description`, `quantity`, `unit_price`, `expiry_date`, `added_on`, `category_id`, `supplier_id`, `img`) VALUES
(1, 'Ibuprofen', 'Advil', 'Nonsteroidal anti-inflammatory drug (NSAID)', 105, 89.85, '2026-12-31', '2025-06-10 18:04:38', 1, NULL, 'Ibruprofen.png'),
(2, 'Acetaminophen ', 'Tylenol', 'Pain reliever and fever reducer', 199, 67.50, '2026-12-31', '2025-06-10 18:04:38', 1, NULL, 'Acetaminophen.png'),
(3, 'Aspirin', 'Bayer', 'Used for pain, fever, and inflammation', 84, 59.85, '2026-12-31', '2025-06-10 18:04:38', 1, NULL, 'Aspirin.png'),
(4, 'Naproxen', 'Aleve', 'NSAID for inflammation and pain', 73, 93.75, '2026-12-31', '2025-06-10 18:04:38', 1, NULL, 'Naproxen.png'),
(5, 'Diclofenac', 'Voltaren', 'NSAID used to treat pain and inflammation', 51, 105.00, '2026-12-31', '2025-06-10 18:04:38', 1, NULL, 'Diclofenac.png'),
(6, 'Tramadol', 'Ultram', 'Opioid pain medication', 30, 194.85, '2026-12-31', '2025-06-10 18:04:38', 1, NULL, 'Tramadol.png'),
(7, 'Morphine', 'MS Contin', 'Strong opioid painkiller', 49, 225.00, '2026-12-31', '2025-06-10 18:04:38', 1, NULL, 'Morphine.png'),
(8, 'Codeine', 'Tylenol #3', 'Mild to moderate pain relief', 80, 131.25, '2026-12-31', '2025-06-10 18:04:38', 1, NULL, 'Codeine.png'),
(9, 'Ketorolac', 'Toradol', 'Short-term pain management', 67, 138.00, '2026-12-31', '2025-06-10 18:04:38', 1, NULL, 'Ketorolac.png'),
(10, 'Penicillin', 'Pfizerpen', 'Antibiotic used for bacterial infections', 120, 72.00, '2026-12-31', '2025-06-10 18:04:38', 2, NULL, 'Penicillin.png'),
(11, 'Amoxicillin', 'Amoxil', 'Broad-spectrum penicillin antibiotic', 100, 82.50, '2026-12-31', '2025-06-10 18:04:38', 2, NULL, 'Amoxicillin.png'),
(12, 'Ciprofloxacin', 'Cipro', 'Fluoroquinolone antibiotic', 80, 93.00, '2026-12-31', '2025-06-10 18:04:38', 2, NULL, 'Ciprofloxacin.png'),
(13, 'Doxycycline', 'Vibramycin', 'Tetracycline antibiotic', 85, 101.25, '2026-12-31', '2025-06-10 18:04:38', 2, NULL, 'Doxycycline.png'),
(14, 'Azithromycin', 'Zithromax', 'Macrolide antibiotic', 95, 105.00, '2026-12-31', '2025-06-10 18:04:38', 2, NULL, 'Azithromycin.png'),
(15, 'Cephalexin', 'Keflex', 'Cephalosporin antibiotic', 100, 88.50, '2026-12-31', '2025-06-10 18:04:38', 2, NULL, 'Cephalexin.png'),
(16, 'Clindamycin', 'Cleocin', 'Lincosamide antibiotic', 90, 102.00, '2026-12-31', '2025-06-10 18:04:38', 2, NULL, 'Clindamycin.png'),
(17, 'Erythromycin', 'Ery-Tab', 'Macrolide antibiotic', 80, 82.50, '2026-12-31', '2025-06-10 18:04:38', 2, NULL, 'Erythromycin.png'),
(18, 'Metronidazole', 'Flagyl', 'Antibiotic and antiprotozoal', 75, 71.25, '2026-12-31', '2025-06-10 18:04:38', 2, NULL, 'Metronidazole.png'),
(19, 'Fluoxetine', 'Prozac', 'SSRI used to treat depression', 100, 127.50, '2026-12-31', '2025-06-10 18:04:38', 3, NULL, 'Fluoxetine.png'),
(20, 'Sertraline', 'Zoloft', 'SSRI for depression and anxiety', 95, 123.75, '2026-12-31', '2025-06-10 18:04:38', 3, NULL, 'Sertraline.png'),
(21, 'Citalopram', 'Celexa', 'SSRI for mood disorders', 90, 118.50, '2026-12-31', '2025-06-10 18:04:38', 3, NULL, 'Citalopram.png'),
(22, 'Paroxetine', 'Paxil', 'SSRI for depression and OCD', 85, 114.00, '2026-12-31', '2025-06-10 18:04:38', 3, NULL, 'Paroxetine.png'),
(23, 'Venlafaxine', 'Effexor', 'SNRI for depression', 80, 135.00, '2026-12-31', '2025-06-10 18:04:38', 3, NULL, 'Venlafaxine.png'),
(24, 'Duloxetine', 'Cymbalta', 'SNRI for depression and anxiety', 75, 142.50, '2026-12-31', '2025-06-10 18:04:38', 3, NULL, 'Duloxetine.png'),
(25, 'Bupropion', 'Wellbutrin', 'Atypical antidepressant', 70, 150.00, '2026-12-31', '2025-06-10 18:04:38', 3, NULL, 'Bupropion.png'),
(26, 'Amitriptyline', 'Elavil', 'Tricyclic antidepressant', 65, 100.50, '2026-12-31', '2025-06-10 18:04:38', 3, NULL, 'Amitriptyline.png'),
(27, 'Escitalopram', 'Lexapro', 'SSRI for depression and anxiety', 90, 124.50, '2026-12-31', '2025-06-10 18:04:38', 3, NULL, 'Escitalopram.png'),
(28, 'Insulin', 'Humulin', 'Hormone for blood sugar control', 200, 375.00, '2026-12-31', '2025-06-10 18:04:38', 4, NULL, 'Insulin.png'),
(29, 'Metformin', 'Glucophage', 'First-line treatment for type 2 diabetes', 180, 108.75, '2026-12-31', '2025-06-10 18:04:38', 4, NULL, 'Metformin.png'),
(30, 'Glipizide', 'Glucotrol', 'Sulfonylurea to lower blood glucose', 150, 90.00, '2026-12-31', '2025-06-10 18:04:38', 4, NULL, 'Glipizide.png'),
(31, 'Glyburide', 'Diabeta', 'Oral diabetes medication', 140, 91.50, '2026-12-31', '2025-06-10 18:04:38', 4, NULL, 'Glyburide.png'),
(32, 'Pioglitazone', 'Actos', 'Increases insulin sensitivity', 130, 117.00, '2026-12-31', '2025-06-10 18:04:38', 4, NULL, 'Pioglitazone.png'),
(33, 'Sitagliptin', 'Januvia', 'DPP-4 inhibitor for diabetes', 125, 138.00, '2026-12-31', '2025-06-10 18:04:38', 4, NULL, 'Sitagliptin.png'),
(34, 'Empagliflozin', 'Jardiance', 'SGLT2 inhibitor for blood sugar control', 120, 157.50, '2026-12-31', '2025-06-10 18:04:38', 4, NULL, 'Empagliflozin.png'),
(35, 'Liraglutide', 'Victoza', 'GLP-1 receptor agonist', 110, 225.00, '2026-12-31', '2025-06-10 18:04:38', 4, NULL, 'Liraglutide.png'),
(36, 'Acarbose', 'Precose', 'Slows carbohydrate digestion', 100, 89.25, '2026-12-31', '2025-06-10 18:04:38', 4, NULL, 'Acarbose.png'),
(37, 'Phenytoin', 'Dilantin', 'Controls seizures', 90, 102.00, '2026-12-31', '2025-06-10 18:04:38', 5, NULL, 'Phenytoin.png'),
(38, 'Lamotrigine', 'Lamictal', 'Used for epilepsy and bipolar disorder', 85, 118.50, '2026-12-31', '2025-06-10 18:04:38', 5, NULL, 'Lamotrigine.png'),
(39, 'Valproic acid', 'Depakote', 'Seizure and mood stabilizer', 80, 127.50, '2026-12-31', '2025-06-10 18:04:38', 5, NULL, 'Valproic_acid.png'),
(40, 'Carbamazepine', 'Tegretol', 'Anticonvulsant and mood stabilizer', 75, 99.00, '2026-12-31', '2025-06-10 18:04:38', 5, NULL, 'Carbamazepine.png'),
(41, 'Levetiracetam', 'Keppra', 'Anticonvulsant', 70, 135.00, '2026-12-31', '2025-06-10 18:04:38', 5, NULL, 'Levetiracetam.png'),
(42, 'Topiramate', 'Topamax', 'Used for epilepsy and migraines', 65, 124.50, '2026-12-31', '2025-06-10 18:04:38', 5, NULL, 'Topiramate.png'),
(43, 'Gabapentin', 'Neurontin', 'Treats nerve pain and seizures', 60, 108.75, '2026-12-31', '2025-06-10 18:04:38', 5, NULL, 'Gabapentin.png'),
(44, 'Clonazepam', 'Klonopin', 'Benzodiazepine used for seizures', 55, 120.00, '2026-12-31', '2025-06-10 18:04:38', 5, NULL, 'Clonazepam.png'),
(45, 'Phenobarbital', 'Luminal', 'Long-acting barbiturate', 50, 106.50, '2026-12-31', '2025-06-10 18:04:38', 5, NULL, 'Phenobarbital.png'),
(46, 'Haloperidol', 'Haldol', 'Typical antipsychotic', 80, 103.50, '2026-12-31', '2025-06-10 18:04:38', 6, NULL, 'Haloperidol.png'),
(47, 'Risperidone', 'Risperdal', 'Atypical antipsychotic', 75, 117.00, '2026-12-31', '2025-06-10 18:04:38', 6, NULL, 'Risperidone.png'),
(48, 'Olanzapine', 'Zyprexa', 'Atypical antipsychotic', 70, 126.00, '2026-12-31', '2025-06-10 18:04:38', 6, NULL, 'Olanzapine.png'),
(49, 'Quetiapine', 'Seroquel', 'Treats schizophrenia and bipolar', 65, 138.00, '2026-12-31', '2025-06-10 18:04:38', 6, NULL, 'Quetiapine.png'),
(50, 'Aripiprazole', 'Abilify', 'Atypical antipsychotic', 60, 157.50, '2026-12-31', '2025-06-10 18:04:38', 6, NULL, 'Aripiprazole.png'),
(51, 'Clozapine', 'Clozaril', 'Used in treatment-resistant schizophrenia', 55, 180.00, '2026-12-31', '2025-06-10 18:04:38', 6, NULL, 'Clozapine.png'),
(52, 'Chlorpromazine', 'Thorazine', 'First-generation antipsychotic', 50, 97.50, '2026-12-31', '2025-06-10 18:04:38', 6, NULL, 'Chlorpromazine.png'),
(53, 'Ziprasidone', 'Geodon', 'Second-generation antipsychotic', 45, 136.50, '2026-12-31', '2025-06-10 18:04:38', 6, NULL, 'Ziprasidone.png'),
(54, 'Lurasidone', 'Latuda', 'Atypical antipsychotic', 40, 165.00, '2026-12-31', '2025-06-10 18:04:38', 6, NULL, 'Lurasidone.png'),
(55, 'Acyclovir', 'Zovirax', 'Antiviral for herpes viruses', 100, 97.50, '2026-12-31', '2025-06-10 18:04:38', 7, NULL, 'Acyclovir.png'),
(56, 'Oseltamivir (Tamiflu)', 'Tamiflu', 'Treats influenza', 90, 131.25, '2026-12-31', '2025-06-10 18:04:38', 7, NULL, 'Oseltamivir.png'),
(57, 'Valacyclovir', 'Valtrex', 'Antiviral for herpes and shingles', 80, 118.50, '2026-12-31', '2025-06-10 18:04:38', 7, NULL, 'Valacyclovir.png'),
(58, 'Remdesivir', 'Veklury', 'Antiviral for COVID-19', 70, 375.00, '2026-12-31', '2025-06-10 18:04:38', 7, NULL, 'Remdesivir.png'),
(59, 'Zidovudine (AZT)', 'Retrovir', 'HIV antiretroviral', 60, 153.75, '2026-12-31', '2025-06-10 18:04:38', 7, NULL, 'Zidovudine.png'),
(60, 'Sofosbuvir', 'Sovaldi', 'Used to treat hepatitis C', 50, 450.00, '2026-12-31', '2025-06-10 18:04:38', 7, NULL, 'Sofosbuvir.png'),
(61, 'Lamivudine', 'Epivir', 'HIV and hepatitis B treatment', 45, 141.00, '2026-12-31', '2025-06-10 18:04:38', 7, NULL, 'Lamivudine.png'),
(62, 'Abacavir', 'Ziagen', 'HIV medication', 40, 176.25, '2026-12-31', '2025-06-10 18:04:38', 7, NULL, 'Abacavir.png'),
(63, 'Tenofovir', 'Viread', 'Antiretroviral for HIV', 35, 187.50, '2026-12-31', '2025-06-10 18:04:38', 7, NULL, 'Tenofovir.png'),
(64, 'Warfarin', 'Coumadin', 'Vitamin K antagonist', 120, 63.00, '2026-12-31', '2025-06-10 18:04:38', 8, NULL, 'Warfarin.png'),
(65, 'Heparin', 'Heparin Sodium', 'Injectable anticoagulant', 110, 76.50, '2026-12-31', '2025-06-10 18:04:38', 8, NULL, 'Heparin.png'),
(66, 'Enoxaparin', 'Lovenox', 'Low molecular weight heparin', 100, 123.75, '2026-12-31', '2025-06-10 18:04:38', 8, NULL, 'Enoxaparin.png'),
(67, 'Dabigatran', 'Pradaxa', 'Direct thrombin inhibitor', 90, 142.50, '2026-12-31', '2025-06-10 18:04:38', 8, NULL, 'Dabigatran.png'),
(68, 'Apixaban', 'Eliquis', 'Factor Xa inhibitor', 80, 150.00, '2026-12-31', '2025-06-10 18:04:38', 8, NULL, 'Apixaban.png'),
(69, 'Rivaroxaban', 'Xarelto', 'Oral anticoagulant', 70, 157.50, '2026-12-31', '2025-06-10 18:04:38', 8, NULL, 'Rivaroxaban.png'),
(70, 'Fondaparinux', 'Arixtra', 'Synthetic anticoagulant', 60, 165.00, '2026-12-31', '2025-06-10 18:04:38', 8, NULL, 'Fondaparinux.png'),
(71, 'Edoxaban', 'Savaysa', 'Oral anticoagulant', 50, 148.50, '2026-12-31', '2025-06-10 18:04:38', 8, NULL, 'Edoxaban.png'),
(72, 'Aspirin', 'Ecotrin', 'Low-dose antiplatelet', 200, 37.50, '2026-12-31', '2025-06-10 18:04:38', 8, NULL, 'Aspirin.png'),
(73, 'Diphenhydramine', 'Benadryl', 'First-generation antihistamine', 150, 60.00, '2026-12-31', '2025-06-10 18:04:38', 9, NULL, 'Diphenhydramine.png'),
(74, 'Loratadine', 'Claritin', 'Second-generation antihistamine', 140, 75.00, '2026-12-31', '2025-06-10 18:04:38', 9, NULL, 'Loratadine.png'),
(75, 'Cetirizine', 'Zyrtec', 'Non-drowsy antihistamine', 129, 82.50, '2026-12-31', '2025-06-10 18:04:38', 9, NULL, 'Cetirizine.png'),
(76, 'Fexofenadine', 'Allegra', 'Second-generation antihistamine', 120, 90.00, '2026-12-31', '2025-06-10 18:04:38', 9, NULL, 'Fexofenadine.png'),
(77, 'Chlorpheniramine', 'Chlor-Trimeton', 'First-gen antihistamine', 110, 56.25, '2026-12-31', '2025-06-10 18:04:38', 9, NULL, 'Chlorpheniramine.png'),
(78, 'Hydroxyzine', 'Vistaril', 'Anxiety and allergy treatment', 100, 97.50, '2026-12-31', '2025-06-10 18:04:38', 9, NULL, 'Hydroxyzine.png'),
(79, 'Levocetirizine', 'Xyzal', 'Non-sedating antihistamine', 90, 88.50, '2026-12-31', '2025-06-10 18:04:38', 9, NULL, 'Levocetirizine.png'),
(80, 'Desloratadine', 'Clarinex', 'Long-acting antihistamine', 80, 93.75, '2026-12-31', '2025-06-10 18:04:38', 9, NULL, 'Desloratadine.png'),
(81, 'Promethazine', 'Phenergan', 'Antihistamine for nausea/allergy', 70, 74.25, '2026-12-31', '2025-06-10 18:04:38', 9, NULL, 'Promethazine.png');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_id` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Paid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sale_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `sale_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sale_id`, `user_id`, `medicine_id`, `quantity`, `total_price`, `sale_date`) VALUES
(17, 2, 11, 10, 55.00, '2025-06-24 11:43:28'),
(18, 2, 12, 10, 62.00, '2025-06-24 11:43:28'),
(19, 2, 8, 1, 8.75, '2025-06-25 15:12:45'),
(20, 2, 9, 1, 9.20, '2025-06-25 15:12:45'),
(21, 2, 6, 1, 12.99, '2025-06-25 15:12:45'),
(22, 16, 1, 1, 5.99, '2026-01-01 15:15:13'),
(23, 16, 2, 1, 4.50, '2026-01-01 15:15:13'),
(24, 16, 2, 1, 4.50, '2026-01-07 00:27:37'),
(25, 16, 3, 1, 3.99, '2026-01-07 00:27:37');

-- --------------------------------------------------------

--
-- Table structure for table `sms_incoming`
--

CREATE TABLE `sms_incoming` (
  `id` int(11) NOT NULL,
  `sender` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `received_at` datetime NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `payment_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact`, `email`, `address`, `category_id`, `created_at`) VALUES
(1, 'HealthPro Distributors', '09171234567', 'contact@healthpro.com', '123 Medical St., QC', 1, '2025-06-10 18:15:54'),
(2, 'PharmaLink Inc.', '09998887766', 'info@pharmalink.ph', '456 Wellness Ave., Makati', 4, '2025-06-10 18:15:54'),
(3, 'MediSupply Co.', '09223334455', 'support@medisupply.com', '789 Botika Blvd., Manila', 1, '2025-06-10 18:15:54');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `userID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `contact` varchar(11) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`userID`, `username`, `email`, `password`, `address`, `contact`, `role`) VALUES
(1, 'admin', NULL, 'admin', NULL, NULL, 'admin'),
(2, 'user', NULL, 'password', NULL, NULL, 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`medicine_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `fk_sales_user` (`user_id`),
  ADD KEY `fk_sales_medicine` (`medicine_id`);

--
-- Indexes for table `sms_incoming`
--
ALTER TABLE `sms_incoming`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order` (`order_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `medicine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `sms_incoming`
--
ALTER TABLE `sms_incoming`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `medicines`
--
ALTER TABLE `medicines`
  ADD CONSTRAINT `medicines_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `sms_incoming`
--
ALTER TABLE `sms_incoming`
  ADD CONSTRAINT `fk_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sms_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
